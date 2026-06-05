<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with('kasir:id,name')->orderBy('created_at', 'desc');

        // Kasir only sees own transactions
        if ($request->user()->role === 'kasir') {
            $query->where('user_id', $request->user()->id);
        }

        // Apply date filters if provided
        $filterMode = $request->query('filter_mode');
        $filterValue = $request->query('filter_value');
        $filterYear = $request->query('filter_year', Carbon::now()->year);

        if ($filterMode) {
            $this->applyDateFilter($query, $filterMode, $filterValue, $filterYear);
        }

        $transaksis = $query->get();
        return response()->json($transaksis);
    }

    /**
     * Apply date filter to a query based on filter_mode and filter_value.
     */
    private function applyDateFilter($query, $filterMode, $filterValue, $filterYear)
    {
        switch ($filterMode) {
            case 'harian':
                $dayMap = [
                    'senin' => Carbon::MONDAY,
                    'selasa' => Carbon::TUESDAY,
                    'rabu' => Carbon::WEDNESDAY,
                    'kamis' => Carbon::THURSDAY,
                    'jumat' => Carbon::FRIDAY,
                    'sabtu' => Carbon::SATURDAY,
                    'minggu' => Carbon::SUNDAY,
                ];
                $dayOfWeek = $dayMap[strtolower($filterValue ?? '')] ?? null;
                if ($dayOfWeek !== null) {
                    $query->whereRaw('DAYOFWEEK(created_at) = ?', [$dayOfWeek % 7 + 1]);
                }
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', $filterYear);
                break;

            case 'mingguan':
                $weekNum = intval($filterValue ?? 1);
                $now = Carbon::now();
                $startOfMonth = Carbon::create($filterYear, $now->month, 1)->startOfDay();

                $weekStart = $startOfMonth->copy()->addWeeks($weekNum - 1);
                $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();

                if ($weekStart->lt($startOfMonth)) {
                    $weekStart = $startOfMonth->copy();
                }
                $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();
                if ($weekEnd->gt($endOfMonth)) {
                    $weekEnd = $endOfMonth->copy();
                }

                $query->whereBetween('created_at', [$weekStart, $weekEnd]);
                break;

            case 'bulanan':
                $month = intval($filterValue ?? Carbon::now()->month);
                $query->whereMonth('created_at', $month)
                      ->whereYear('created_at', $filterYear);
                break;

            case 'tanggal':
                if ($filterValue) {
                    try {
                        $date = Carbon::parse($filterValue);
                        $query->whereDate('created_at', $date->toDateString());
                    } catch (\Exception $e) {
                        $query->whereDate('created_at', Carbon::today()->toDateString());
                    }
                } else {
                    $query->whereDate('created_at', Carbon::today()->toDateString());
                }
                break;
        }

        return $query;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'metode_pembayaran' => 'required|in:tunai,debit,transfer',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produks,id',
            'items.*.qty' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            $total = 0;
            $kode = 'TRX-' . date('Ymd') . '-' . rand(1000, 9999);

            $transaksi = Transaksi::create([
                'user_id' => $request->user()->id,
                'kode_transaksi' => $kode,
                'total' => 0,
                'metode_pembayaran' => $validated['metode_pembayaran']
            ]);

            foreach ($validated['items'] as $item) {
                $produk = Produk::findOrFail($item['produk_id']);
                
                if ($produk->stok < $item['qty']) {
                    throw new \Exception("Stok tidak cukup untuk produk: " . $produk->nama_produk);
                }

                $subtotal = $produk->harga_jual * $item['qty'];
                $total += $subtotal;

                // Create detail
                $transaksi->details()->create([
                    'produk_id' => $produk->id,
                    'qty' => $item['qty'],
                    'harga_satuan' => $produk->harga_jual,
                    'subtotal' => $subtotal
                ]);

                // Reduce stock
                $produk->decrement('stok', $item['qty']);

                // Record history
                $produk->riwayatStok()->create([
                    'tipe' => 'keluar',
                    'qty' => $item['qty'],
                    'sumber' => 'Penjualan',
                    'referensi_id' => $transaksi->id
                ]);
            }

            $transaksi->update(['total' => $total]);

            DB::commit();

            return response()->json($transaksi->load('details.produk'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['details.produk', 'kasir:id,name'])->findOrFail($id);
        return response()->json($transaksi);
    }
}
