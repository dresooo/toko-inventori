<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Menampilkan data stok produk dan bahan baku.
     * Juga melakukan update flagging otomatis & notifikasi stok.
     */
    public function index()
    {
        $products = Product::with('recipes.rawMaterial')->get();
        $materials = RawMaterial::all();

        $stockData = [];

        foreach ($products as $product) {
            // Hitung maksimum produksi berdasarkan stok bahan baku
            if ($product->recipes->isEmpty()) {
                $max = 0;
            } else {
                $max = PHP_INT_MAX;
                foreach ($product->recipes as $recipe) {
                    if ($recipe->rawMaterial && $recipe->quantity_needed > 0) {
                        $available = $recipe->rawMaterial->stock_quantity ?? 0;
                        $max = min($max, floor($available / $recipe->quantity_needed));
                    }
                }
                if ($max === PHP_INT_MAX) {
                    $max = 0;
                }
            }

            // Tentukan flag stok dengan 3 level
            $flag = 'normal';
            if ($max <= 0) {
                $flag = 'critical';
            } elseif ($max <= 25) {
                $flag = 'need_restock';
            } elseif ($max <= 50) {
                $flag = 'low';
            }

                        //  Update flag hanya jika berubah
            if ($product->stock_flag !== $flag) {
                $oldFlag = $product->stock_flag;
                $product->stock_flag = $flag;
                $product->save();

                // Tentukan pesan sesuai dengan kondisi stok
                $statusMessage = match ($flag) {
                    'critical' => "Stok produk '{$product->nama}' sudah habis. Segera lakukan restock!",
                    'need_restock' => "Stok produk '{$product->nama}' sangat menipis. Mohon segera restock sebelum habis.",
                    'low' => "Stok produk '{$product->nama}'  (tersisa {$max} unit). Perhatikan ketersediaannya.",
                    default => null,
                };

                // Kirim notifikasi hanya jika memang perlu
                if ($statusMessage) {
                    $admins = User::where('user_type', 'admin')->get();

                    foreach ($admins as $admin) {
                        Notification::create([
                            'user_id'    => $admin->id,
                            'type'       => 'stock',
                            'message'    => $statusMessage,
                            'is_read'    => 0,
                            'created_at' => now(),
                        ]);
                    }
                }
            }

            // Simpan data produk untuk response
            $stockData[] = [
                'product_id'     => $product->product_id,
                'nama'           => $product->nama,
                'max_production' => $max,
                'stock_flag'     => $flag,
            ];
        }

        return response()->json([
            'products'  => $stockData,
            'materials' => $materials,
        ]);
    }

    /**
     * Mengupdate stok bahan baku.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'stock_quantity' => 'required|numeric|min:0',
        ]);

        $material = RawMaterial::findOrFail($id);
        $material->stock_quantity = $request->input('stock_quantity');
        $material->save();

        return response()->json([
            'message'  => 'Stok bahan baku berhasil diperbarui',
            'material' => $material,
        ]);
    }
}
