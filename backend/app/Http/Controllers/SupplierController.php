<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        // Allow admin, kasir, or owner to view list (e.g. for selection) but restrict mutations
        $suppliers = Supplier::orderBy('nama', 'asc')->get();
        return response()->json($suppliers);
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Hanya admin yang dapat mengelola supplier.'], 403);
        }

        $validated = $request->validate([
            'nama' => 'required|string|unique:suppliers,nama',
            'telepon' => 'nullable|regex:/^[0-9]+$/|max:13',
            'alamat' => 'nullable|string|max:255',
        ]);

        $supplier = Supplier::create($validated);
        return response()->json($supplier, 201);
    }

    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Hanya admin yang dapat mengelola supplier.'], 403);
        }

        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|unique:suppliers,nama,' . $supplier->id,
            'telepon' => 'nullable|regex:/^[0-9]+$/|max:13',
            'alamat' => 'nullable|string|max:255',
        ]);

        $supplier->update($validated);
        return response()->json($supplier);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Hanya admin yang dapat mengelola supplier.'], 403);
        }

        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return response()->json(['message' => 'Supplier berhasil dihapus.']);
    }
}
