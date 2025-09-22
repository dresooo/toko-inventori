<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;

class PaymentController extends Controller
{
    // Ambil detail payment berdasarkan order_id
    public function show($order_id)
    {
        $payment = Payment::where('order_id', $order_id)->first();
        $order   = Order::findOrFail($order_id);

        return response()->json([
            'success' => true,
            'order'   => $order,
            'payment' => $payment,
        ]);
    }

    // Simpan payment baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id'      => 'required|exists:orders,order_id',
            'amount'        => 'required|numeric',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $order = Order::findOrFail($request->order_id);

        // simpan bukti pembayaran (opsional)
        $proof = null;
        if ($request->hasFile('payment_proof')) {
            $proof = file_get_contents($request->file('payment_proof')->getRealPath());
        }

        $payment = Payment::create([
            'order_id'      => $order->order_id,
            'amount'        => $request->amount,
            'payment_date'  => now(),
            'payment_proof' => $proof,
            'status'        => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment created, waiting for verification.',
            'payment' => $payment,
        ]);
 
 
    }

    // Tampilkan halaman pembayaran (Blade)
public function showPage($order_id)
{
    $order = Order::with('product')->findOrFail($order_id);

    // cek kalau sudah ada payment
    $payment = Payment::where('order_id', $order_id)->first();

    return view('payment', compact('order', 'payment'));
}
}
