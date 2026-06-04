<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = Transaksi::with('kasir:id,name')->orderBy('created_at', 'desc')->get();
        return response()->json($transaksis);
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

    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Hanya admin yang dapat menghapus transaksi.'], 403);
        }

        DB::beginTransaction();

        try {
            $transaksi = Transaksi::with('details')->findOrFail($id);

            foreach ($transaksi->details as $detail) {
                $produk = Produk::find($detail->produk_id);
                if ($produk) {
                    // Restore stock
                    $produk->increment('stok', $detail->qty);

                    // Record to stok history as cancellation
                    $produk->riwayatStok()->create([
                        'tipe' => 'masuk',
                        'qty' => $detail->qty,
                        'sumber' => 'Pembatalan Transaksi oleh Admin',
                        'referensi_id' => $transaksi->id
                    ]);
                }
            }

            // Delete transaction (cascades to details)
            $transaksi->delete();

            DB::commit();

            return response()->json([
                'message' => 'Transaksi berhasil dihapus, stok dan keuangan telah dikembalikan.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
