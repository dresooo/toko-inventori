<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RawMaterial;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $products = Product::with('recipes.rawMaterial')->get();
        $materials = RawMaterial::all();

        $stockData = [];
        foreach ($products as $product) {
            $max = PHP_INT_MAX;
            foreach ($product->recipes as $recipe) {
                if ($recipe->rawMaterial->stock_quantity > 0) {
                    $max = min(
                        $max,
                        floor($recipe->rawMaterial->stock_quantity / $recipe->quantity_needed)
                    );
                } else {
                    $max = 0;
                }
            }
            $stockData[] = [
                'product_id' => $product->product_id,   // ✅ sesuai PK di model
                'nama' => $product->nama,
                'max_production' => $max,
            ];
        }

        return response()->json([
            'products' => $stockData,
            'materials' => $materials
        ]);
    }

    public function update(Request $request, $id)
    {
        $material = RawMaterial::findOrFail($id);
        $material->quantity = $request->input('quantity');
        $material->save();

        return response()->json([
            'message' => 'Stok bahan baku berhasil diperbarui',
            'material' => $material
        ]);
    }
}
