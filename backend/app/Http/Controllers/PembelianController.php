<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Hanya admin yang dapat mengelola pembelian.'], 403);
        }

        $pembelians = Pembelian::with('user:id,name')->orderBy('created_at', 'desc')->get();
        return response()->json($pembelians);
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Hanya admin yang dapat mengelola pembelian.'], 403);
        }

        // Support multipart form-data items decode
        if (is_string($request->input('items'))) {
            $request->merge([
                'items' => json_decode($request->input('items'), true)
            ]);
        }

        $validated = $request->validate([
            'supplier' => 'required|string',
            'invoice' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produks,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'struk_file' => 'nullable|image|max:4096'
        ]);

        $strukPath = null;
        if ($request->hasFile('struk_file')) {
            $path = $request->file('struk_file')->store('pembelians', 'public');
            $strukPath = asset('storage/' . $path);
        }

        DB::beginTransaction();

        try {
            $total = 0;

            $pembelian = Pembelian::create([
                'user_id' => $request->user()->id,
                'supplier' => $validated['supplier'],
                'invoice' => $validated['invoice'],
                'total' => 0,
                'struk' => $strukPath
            ]);

            foreach ($validated['items'] as $item) {
                $subtotal = $item['harga_beli'] * $item['qty'];
                $total += $subtotal;

                // Create detail
                $pembelian->details()->create([
                    'produk_id' => $item['produk_id'],
                    'qty' => $item['qty'],
                    'harga_beli' => $item['harga_beli'],
                    'subtotal' => $subtotal
                ]);

                // Update stock and harga beli
                $produk = Produk::find($item['produk_id']);
                $produk->increment('stok', $item['qty']);
                $produk->update(['harga_beli' => $item['harga_beli']]); // update last purchase price

                // Record history
                $produk->riwayatStok()->create([
                    'tipe' => 'masuk',
                    'qty' => $item['qty'],
                    'sumber' => 'Pembelian',
                    'referensi_id' => $pembelian->id
                ]);
            }

            $pembelian->update(['total' => $total]);

            DB::commit();

            return response()->json($pembelian->load('details.produk'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function show(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Hanya admin yang dapat mengelola pembelian.'], 403);
        }

        $pembelian = Pembelian::with(['details.produk', 'user:id,name'])->findOrFail($id);
        return response()->json($pembelian);
    }
}
