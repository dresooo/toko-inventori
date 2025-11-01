<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\User;
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
            'payment_date' => now(),
            'status'       => 'pending', // default payment status
        ];

        // Jika ada file bukti pembayaran
        if ($request->hasFile('payment_proof')) {
            $binary = file_get_contents($request->file('payment_proof')->getRealPath());
            $data['payment_proof'] = base64_encode($binary);
        }

        $payment = Payment::create($data);

        // ✅ Update status order menjadi 'processing' setelah payment dibuat
        $order = Order::find($request->order_id);
        $order->status = 'processing';
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment created successfully, order status updated to processing',
            'payment' => $payment,
        ], 201);
    }

    // Tampilkan halaman pembayaran (Blade)
    public function showPage($order_id)
    {
        $order = Order::with('product')->findOrFail($order_id);
        $payment = Payment::where('order_id', $order_id)->first();

        return view('payment', compact('order', 'payment'));
    }

    // Simpan payment dari web
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
        
        // ✅ Update status order jadi 'processing'
        $order = Order::find($request->order_id);
        $order->status = 'processing';
        $order->save();
        


        // ✅ Buat notifikasi untuk semua admin
        $admins = User::where('user_type', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'    => $admin->id, // user admin
                'type'       => 'payment',
                'message'    => "Pembayaran untuk order #{$order->order_id} berhasil, status order sekarang 'processing'.",
                'is_read'    => 0,
                'created_at' => now(),
            ]);
        }

        return response()->json([
        'success' => true,
        'message' => 'Bukti pembayaran berhasil diunggah. Pesanan Anda sedang diproses.',
        'order_id' => $request->order_id, // Opsional, bisa digunakan di JS
    ], 200); // Pastikan status HTTP adalah 200 OK
    }
}
