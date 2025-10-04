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
        'user_id'       => 'required|exists:users,id',
        'product_id'    => 'required|exists:products,product_id',
        'quantity'      => 'required|integer|min:1',
        'total_amount'  => 'required|integer',
        'full_name'     => 'required|string|max:255',  
        'phone_number'  => 'required|string|max:20',
        'shipping_addr' => 'required|string',
        'custom_gambar' => 'nullable|file|image|max:2048',
    ]);

    try {
        DB::beginTransaction();

        $order = Order::create([
            'user_id'       => $validated['user_id'],
            'product_id'    => $validated['product_id'],
            'quantity'      => $validated['quantity'],
            'total_amount'  => $validated['total_amount'],
            'full_name'     => $validated['full_name'],    
            'phone_number'  => $validated['phone_number'],
            'shipping_addr' => $validated['shipping_addr'],
            'status'        => 'awaiting_payment',
        ]);

        // Upload custom gambar (file upload manual)
        if ($request->hasFile('custom_gambar')) {
            $file = $request->file('custom_gambar');
            $path = $file->store('uploads', 'public');
            $order->custom_gambar = $path;
        }

        // Upload custom canvas (hasil dari toDataURL -> file)
        if ($request->hasFile('custom_canvas')) {
            $file = $request->file('custom_canvas');
            $path = $file->store('uploads/canvas', 'public');
            $order->custom_canvas = $path;
        }

        $order->save();

        // Kurangi stok bahan baku
        $this->reduceStockByRecipe($validated['product_id'], $validated['quantity']);

        DB::commit();

        return response()->json([
            'success'     => true,
            'order'       => $order,
            'payment_url' => url("/payment/{$order->order_id}")
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
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
            throw new \Exception("Stok tidak cukup");
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



// List semua order untuk admin
// List semua order untuk admin
public function adminIndex()
{
    // Pilih kolom yang aman untuk JSON (exclude BLOB)
    $orders = Order::with([
        'user:id,name,email',
        'product:product_id,nama,harga',
        'payment:payment_id,order_id,payment_proof,status,payment_date' // 
    ])
        ->select([
            'order_id',
            'user_id',
            'full_name',
            'phone_number',
            'product_id',
            'quantity',
            'total_amount',
            'order_date',
            'status',
            'shipping_addr',
            'custom_gambar',  
            'created_at',
            'updated_at'
        ])
        ->get();

    return response()->json($orders, 200, [], JSON_UNESCAPED_UNICODE);
}


// Update status order khusus admin
public function adminUpdateStatus(Request $request, $id)
{
    $order = Order::findOrFail($id);

    $request->validate([
        // Sesuaikan validasi dengan ENUM di DB
        'status' => 'required|in:paid,processing,shipped,cancelled',
    ]);

    $order->status = $request->status;
    $order->save();

    return response()->json([
        'message' => 'Status order berhasil diperbarui oleh admin',
        'order'   => $order,
    ]);
}

public function adminShow($id)
{
    $order = Order::with([
        'user:id,name,email',
        'product:product_id,nama,harga',
        'payment:payment_id,order_id,payment_proof,status,payment_date' 
    ])
    ->select([
        'order_id',
        'user_id',
        'full_name',
        'phone_number',
        'product_id',
        'quantity',
        'total_amount',
        'order_date',
        'status',
        'shipping_addr',
        'custom_gambar', 
        'created_at',
        'updated_at'
    ])
    ->findOrFail($id);

    // Pastikan string valid UTF-8
    $order->full_name = mb_convert_encoding($order->full_name, 'UTF-8', 'UTF-8');
    $order->shipping_addr = mb_convert_encoding($order->shipping_addr, 'UTF-8', 'UTF-8');

    return response()->json($order, 200, [], JSON_UNESCAPED_UNICODE);
}




// === Update status order + payment dari sisi admin ===
public function adminUpdateOrderPaymentStatus(Request $request, $id)
{
    $request->validate([
        'action' => 'required|in:verified,rejected,shipped'
    ]);

    $order = Order::with('payment')->findOrFail($id);

    // cek apakah order punya payment
    if (!$order->payment) {
        return response()->json([
            'message' => 'Payment record not found for this order'
        ], 400);
    }

    switch ($request->action) {
        case 'verified':
            if ($order->status === 'processing' && $order->payment->status === 'pending') {
                $order->status = 'paid';
                $order->payment->status = 'verified';
            }
            break;

        case 'rejected':
            if ($order->status === 'processing' && $order->payment->status === 'pending') {
                $order->status = 'cancelled';
                $order->payment->status = 'rejected';
            }
            break;

        case 'shipped':
            if ($order->status === 'paid') {
                $order->status = 'shipped';
            }
            break;
    }

    // simpan perubahan
    $order->save();
    $order->payment->save();

    // refresh data agar yang dikirim ke response paling baru
    $order->refresh()->load('payment');

    return response()->json([
        'message' => 'Status berhasil diperbarui',
        'order'   => $order
    ]);
}

}