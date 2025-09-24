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

    // Encode binary ke base64 
    if ($payment && $payment->payment_proof) {
        $payment->payment_proof = base64_encode($payment->payment_proof);
    }

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

    $data = [
        'order_id'     => $request->order_id,
        'amount'       => $request->amount,
        'payment_date' => now(),          // ✅ wajib diisi
        'status'       => 'pending',      // ✅ default status
    ];

    // Jika ada file bukti pembayaran
    if ($request->hasFile('payment_proof')) {
        $binary = file_get_contents($request->file('payment_proof')->getRealPath());

        // Simpan sebagai base64 agar tidak error UTF-8
        $data['payment_proof'] = base64_encode($binary);
    }

    $payment = Payment::create($data);

    return response()->json([
        'success' => true,
        'message' => 'Payment created successfully',
        'payment' => $payment,
    ], 201);
}


    // Tampilkan halaman pembayaran (Blade)
public function showPage($order_id)
{
    $order = Order::with('product')->findOrFail($order_id);

    // cek kalau sudah ada payment
    $payment = Payment::where('order_id', $order_id)->first();

    return view('payment', compact('order', 'payment'));
}


public function storeWeb(Request $request)
{
    $validated = $request->validate([
        'order_id' => 'required|exists:orders,order_id',
        'amount' => 'required|numeric',
        'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ]);

    $data = [
        'order_id' => $request->order_id,
        'amount' => $request->amount,
        'payment_date' => now(),
        'status' => 'pending',
    ];

    if ($request->hasFile('payment_proof')) {
        $binary = file_get_contents($request->file('payment_proof')->getRealPath());
        $data['payment_proof'] = base64_encode($binary);
    }

    Payment::create($data);

    return redirect()->back()->with('payment_success', true);
}
}
