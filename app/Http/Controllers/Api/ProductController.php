<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // GET semua produk
public function index()
{
    $products = Product::with('recipes.rawMaterial')->get()->map(function ($product) {
        if ($product->gambar) {
            $product->gambar = base64_encode($product->gambar);
        }
        return $product;
    });

    return response()->json($products, 200, [], JSON_UNESCAPED_UNICODE);
}

// GET detail produk
public function show($id)
{
    $product = Product::with('recipes.rawMaterial')->find($id);
    if (!$product) return response()->json(['message' => 'Not Found'], 404);

    if ($product->gambar) {
        $product->gambar = base64_encode($product->gambar);
    }

    return response()->json($product, 200, [], JSON_UNESCAPED_UNICODE);
}


    // POST tambah produk
public function store(Request $request)
{
    $data = $request->all();

    if ($request->hasFile('gambar')) {
        $data['gambar'] = file_get_contents($request->file('gambar')->getRealPath());
    }

    $product = Product::create($data);

    if ($product->gambar) {
        $product->gambar = base64_encode($product->gambar);
    }

    return response()->json($product, 201);
}



//Update PRoduct
public function update(Request $request, $id)
{
    $product = Product::find($id);
    if (!$product) return response()->json(['message' => 'Not Found'], 404);

    // Ambil field yang diizinkan saja
    $data = $request->only(['nama', 'harga', 'stok', 'deskripsi']);

    // Handle gambar baru
    if ($request->hasFile('gambar')) {
        $data['gambar'] = file_get_contents($request->file('gambar')->getRealPath());
    }

    $product->update($data);

    // Untuk response JSON, encode base64 tapi tidak disimpan ke DB
    $productJson = $product->toArray();
    if ($product->gambar) {
        $productJson['gambar'] = base64_encode($product->gambar);
    }

    return response()->json($productJson, 200);
}

    // DELETE hapus produk
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['message' => 'Not Found'], 404);

        $product->delete();
        return response()->json(null, 204);
    }


    
}


