<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
class OrderController extends Controller
{
    // Simpan order baru
    public function store(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
        'product_id' => 'required|exists:products,product_id',
        'quantity' => 'required|integer|min:1',
        'total_amount' => 'required|integer',
        'shipping_addr' => 'required|string',
        'custom_gambar' => 'nullable|file|image|max:2048'
    ]);

    try {
        DB::beginTransaction(); // mulai transaction

        // ------------------------------
        // Simpan order dulu
        // ------------------------------
        $order = Order::create([
            'user_id' => $validated['user_id'],
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'total_amount' => $validated['total_amount'],
            'shipping_addr' => $validated['shipping_addr'],
        ]);

        // Upload custom gambar jika ada
        if ($request->hasFile('custom_gambar')) {
            $file = $request->file('custom_gambar');
            $path = $file->store('uploads', 'public');
            $order->custom_gambar = $path;
            $order->save();
        }

        // ------------------------------
        // Kurangi stok berdasarkan recipe
        // ------------------------------
        $this->reduceStockByRecipe($validated['product_id'], $validated['quantity']);

        DB::commit(); // commit transaction

        // ------------------------------
        // Return response beserta redirect URL payment
        // ------------------------------
        return response()->json([
            'success' => true,
            'order' => $order,
            'payment_url' => url("/payment/{$order->id}") // ganti sesuai route payment kamu
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack(); // batal semua jika error
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
}

    // Lihat detail order
    public function show($id)
    {
        $order = Order::with(['user', 'product'])->findOrFail($id);

        return response()->json($order);
    }

    // List semua order
    public function index()
    {
        $orders = Order::with(['user', 'product'])->get();

        return response()->json($orders);
    }

    // Update status order
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,delivered,cancelled',
        ]);

        $order->status = $request->status;
        $order->save();

        return response()->json([
            'message' => 'Status berhasil diperbarui',
            'order'   => $order,
        ]);
    }

    // === Function kurangi stok  ===
private function reduceStockByRecipe($productId, $orderQuantity)
{
    $product = Product::with('recipes.rawMaterial')->findOrFail($productId);

    // Cek stok
    foreach ($product->recipes as $recipe) {
        $neededQty = $recipe->quantity_needed * $orderQuantity;
        if ($recipe->rawMaterial->stock_quantity < $neededQty) {
            throw new \Exception("Stok {$recipe->rawMaterial->nama} tidak cukup");
        }
    }

    // Kurangi stok
    foreach ($product->recipes as $recipe) {
        $neededQty = $recipe->quantity_needed * $orderQuantity;
        $recipe->rawMaterial->decrement('stock_quantity', $neededQty);
    }
}

// Tampilkan form order berdasarkan product id
public function create($productId)
{
    $product = Product::findOrFail($productId);

    // lempar ke blade (resources/views/order.blade.php)
    return view('order', compact('product'));
}
}