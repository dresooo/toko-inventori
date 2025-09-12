<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RawMaterial;

class RawMaterialController extends Controller
{
    // GET semua bahan baku
    public function index()
    {
        return response()->json(RawMaterial::all(), 200);
    }

    // GET detail bahan baku
    public function show($id)
    {
        $material = RawMaterial::find($id);
        if (!$material) return response()->json(['message' => 'Not Found'], 404);

        return response()->json($material, 200);
    }

    // POST tambah bahan baku
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'stock_quantity' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'harga_beli' => 'nullable|numeric|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'status' => 'in:active,inactive',
        ]);

        $material = RawMaterial::create($validated);
        return response()->json($material, 201);
    }

    // PUT/PATCH update bahan baku
    public function update(Request $request, $id)
    {
        $material = RawMaterial::find($id);
        if (!$material) return response()->json(['message' => 'Not Found'], 404);

        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:100',
            'stock_quantity' => 'sometimes|required|integer|min:0',
            'satuan' => 'sometimes|required|string|max:20',
            'harga_beli' => 'nullable|numeric|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'status' => 'in:active,inactive',
        ]);

        $material->update($validated);
        return response()->json($material, 200);
    }

    // DELETE hapus bahan baku
    public function destroy($id)
    {
        $material = RawMaterial::find($id);
        if (!$material) return response()->json(['message' => 'Not Found'], 404);

        $material->delete();
        return response()->json(null, 204);
    }
}
