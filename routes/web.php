<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/homepage', function () {
    return view('homepage',);
});

Route::get('/dashboard', function () {
    return view('dashboard',);
});


// routes untuk isi dashboard
// Halaman product di dalam dashboard
Route::get('/dashboard/product', function () {
    return view('dashboard.product');
})->name('dashboard.product');

Route::get('/dashboard/rawmaterial', function () {
    return view('dashboard.rawmaterial');
})->name('dashboard.rawmaterial');

Route::get('/dashboard/productrecipe', function () {
    return view('dashboard.productrecipe');
})->name('dashboard.productrecipe');

Route::get('/dashboard/stock', function () {
    return view('dashboard.stock');
})->name('dashboard.stock');

// web.php untuk order admin
Route::get('/dashboard/orderadmin', function () {
    return view('dashboard.orderadmin');
})->name('dashboard.orderadmin');

// web.php untuk notification
Route::get('/dashboard/notification', function () {
    return view('dashboard.notification');
})->name('dashboard.notification');

// web.php untuk notification
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');





// render form order button dari homepage saat di click
Route::get('/order/{productId}', function ($productId) {
    $product = Product::findOrFail($productId);
    return view('order', compact('product')); // kirim langsung product
})->name('order.form');


Route::get('/order/{productId}', [OrderController::class, 'create'])->name('order.form'); // untuk render form
Route::post('/order', [OrderController::class, 'store'])->name('order.store'); // untuk submit order




// Tampilkan halaman pembayaran (Blade)
Route::get('/payment/{order_id}', [PaymentController::class, 'showPage'])
    ->name('payment.page');

Route::post('/payments/web', [PaymentController::class, 'storeWeb'])->name('payments.storeWeb');

//order histor
// halaman order history detail (frontend view)
Route::get('/orderhistory/{orderId}', function () {
    return view('orderhistory');
});


