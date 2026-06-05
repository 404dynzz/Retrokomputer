<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Pembelian;
use App\Models\Produk;
use App\Models\RiwayatStok;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function dashboard(Request $request)
    {
        $role = $request->user()->role;

        // Build date filter based on filter_mode
        $filterMode = $request->query('filter_mode');
        $filterValue = $request->query('filter_value');
        $filterYear = $request->query('filter_year', Carbon::now()->year);

        $penjualanQuery = Transaksi::query();
        $totalTransaksiQuery = Transaksi::query();

        // Apply role-based filter for kasir
        if ($role === 'kasir') {
            $penjualanQuery = $penjualanQuery->where('user_id', $request->user()->id);
            $totalTransaksiQuery = $totalTransaksiQuery->where('user_id', $request->user()->id);
        }

        // Apply date filters
        $this->applyDateFilter($penjualanQuery, $filterMode, $filterValue, $filterYear);
        $this->applyDateFilter($totalTransaksiQuery, $filterMode, $filterValue, $filterYear);

        $penjualanBulanIni = $penjualanQuery->sum('total');
        $totalTransaksi = $totalTransaksiQuery->count();

        // Kasir tidak boleh melihat Pembelian, Laba Bersih, dan Kerugian Inventaris
        if ($role === 'kasir') {
            return response()->json([
                'penjualan_bulan_ini' => $penjualanBulanIni,
                'pembelian_bulan_ini' => 0,
                'laba_bersih' => 0,
                'total_transaksi' => $totalTransaksi,
                'kerugian_inventaris' => 0
            ]);
        }

        $pembelianQuery = Pembelian::query();
        $this->applyDateFilter($pembelianQuery, $filterMode, $filterValue, $filterYear);
        $pembelianBulanIni = $pembelianQuery->sum('total');

        // Riwayat stok rusak/hilang
        $kerugianQuery = RiwayatStok::whereIn('sumber', ['Barang Rusak', 'Barang Hilang']);
        $this->applyDateFilter($kerugianQuery, $filterMode, $filterValue, $filterYear);
        $kerugianInventaris = $kerugianQuery->with('produk')
                                            ->get()
                                            ->sum(function ($item) {
                                                return $item->qty * $item->produk->harga_beli;
                                            });

        $labaBersih = $penjualanBulanIni - $pembelianBulanIni - $kerugianInventaris;

        return response()->json([
            'penjualan_bulan_ini' => $penjualanBulanIni,
            'pembelian_bulan_ini' => $pembelianBulanIni,
            'laba_bersih' => $labaBersih,
            'total_transaksi' => $totalTransaksi,
            'kerugian_inventaris' => $kerugianInventaris
        ]);
    }

    /**
     * Apply date filter to a query based on filter_mode and filter_value.
     * If no filter_mode is provided, defaults to current month.
     */
    private function applyDateFilter($query, $filterMode, $filterValue, $filterYear)
    {
        switch ($filterMode) {
            case 'harian':
                // filter_value = day name in Indonesian: senin, selasa, rabu, kamis, jumat, sabtu, minggu
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
                // Also scope to current month by default
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', $filterYear);
                break;

            case 'mingguan':
                // filter_value = 1, 2, 3, 4 (week number in current month)
                $weekNum = intval($filterValue ?? 1);
                $now = Carbon::now();
                $startOfMonth = Carbon::create($filterYear, $now->month, 1)->startOfDay();

                $weekStart = $startOfMonth->copy()->addWeeks($weekNum - 1);
                $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();

                // Clamp to month boundaries
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
                // filter_value = 1-12 (month number)
                $month = intval($filterValue ?? Carbon::now()->month);
                $query->whereMonth('created_at', $month)
                      ->whereYear('created_at', $filterYear);
                break;

            case 'tanggal':
                // filter_value = YYYY-MM-DD (exact date)
                if ($filterValue) {
                    try {
                        $date = Carbon::parse($filterValue);
                        $query->whereDate('created_at', $date->toDateString());
                    } catch (\Exception $e) {
                        // Invalid date, default to today
                        $query->whereDate('created_at', Carbon::today()->toDateString());
                    }
                } else {
                    $query->whereDate('created_at', Carbon::today()->toDateString());
                }
                break;

            default:
                // Default: current month
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
                break;
        }

        return $query;
    }

    public function stok(Request $request)
    {
        if ($request->user()->role !== 'admin' && $request->user()->role !== 'owner') {
            return response()->json(['message' => 'Hanya admin atau owner yang dapat melihat laporan stok.'], 403);
        }

        $produks = Produk::all();
        $stokAman = $produks->where('stok', '>', 5)->count();
        $stokRendah = $produks->where('stok', '>', 0)->where('stok', '<=', 5)->count();
        $stokHabis = $produks->where('stok', '<=', 0)->count();

        return response()->json([
            'total_jenis_produk' => $produks->count(),
            'stok_aman' => $stokAman,
            'stok_rendah' => $stokRendah,
            'stok_habis' => $stokHabis
        ]);
    }

    public function riwayatStok(Request $request)
    {
        if ($request->user()->role !== 'admin' && $request->user()->role !== 'owner') {
            return response()->json(['message' => 'Hanya admin atau owner yang dapat melihat riwayat stok.'], 403);
        }

        $query = RiwayatStok::with('produk')->orderBy('created_at', 'desc');

        if ($request->has('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        return response()->json($query->get());
    }
}
