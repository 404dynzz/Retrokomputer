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
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;
        $role = $request->user()->role;

        $penjualanBulanIni = Transaksi::whereMonth('created_at', $bulanIni)
                                      ->whereYear('created_at', $tahunIni);
        
        // Jika kasir, filter penjualan miliknya sendiri agar kasir tidak memantau penjualan kasir lain
        if ($role === 'kasir') {
            $penjualanBulanIni = $penjualanBulanIni->where('user_id', $request->user()->id);
        }
        
        $penjualanBulanIni = $penjualanBulanIni->sum('total');

        $totalTransaksi = Transaksi::whereMonth('created_at', $bulanIni)
                                   ->whereYear('created_at', $tahunIni);
                                   
        if ($role === 'kasir') {
            $totalTransaksi = $totalTransaksi->where('user_id', $request->user()->id);
        }
        
        $totalTransaksi = $totalTransaksi->count();

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

        $pembelianBulanIni = Pembelian::whereMonth('created_at', $bulanIni)
                                      ->whereYear('created_at', $tahunIni)
                                      ->sum('total');

        // Riwayat stok rusak/hilang (asumsi sumber "Barang Rusak" atau "Barang Hilang")
        $kerugianInventaris = RiwayatStok::whereIn('sumber', ['Barang Rusak', 'Barang Hilang'])
                                         ->whereMonth('created_at', $bulanIni)
                                         ->whereYear('created_at', $tahunIni)
                                         ->with('produk')
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
