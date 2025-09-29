<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\RawMaterialController;
use App\Http\Controllers\Api\ProductRecipeController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;

// Untuk API Product
Route::apiResource('products', ProductController::class);
// Edit / Update product
Route::put('/products/{id}', [ProductController::class, 'update']);
//Untuk Api Login Register
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
//ambil prodcut list
Route::get('/products/list', [ProductController::class, 'list']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

     // Update Profil Put
    Route::put('/user/{id}', [AuthController::class, 'update']);
});

Route::delete('/products/{id}', [ProductController::class, 'destroy']);


//Routes API STOCK
Route::get('/stocks', [StockController::class, 'index']);
Route::put('/stocks/{id}', [StockController::class, 'update']);

//ROUTES API RAW MATERIALS
Route::apiResource('raw-materials', RawMaterialController::class);

//ROUTES API PRODUCT RECIPESS
Route::get('/product-recipe', [ProductRecipeController::class, 'index']);
Route::put('/product-recipe/{id}', [ProductRecipeController::class, 'update']);
Route::post('/product-recipe', [ProductRecipeController::class, 'store']);
Route::get('/product-recipe/{productId}', [ProductRecipeController::class, 'getByProduct']);
Route::put('/product-recipe', [ProductRecipeController::class, 'updateByProductAndMaterial']);
Route::delete('/product-recipe/{id}', [ProductRecipeController::class, 'destroy']);
// Hapus semua recipe berdasarkan product_id
Route::delete('/product-recipe/product/{productId}', [ProductRecipeController::class, 'destroyByProduct']);



//routesa api order
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);

    // admin order
    Route::get('/admin/orders', [OrderController::class, 'adminIndex']);
    Route::put('/admin/orders/{id}', [OrderController::class, 'adminUpdateStatus']);
    Route::get('/admin/orders/{id}', [OrderController::class, 'adminShow']); 
});


Route::post('/payments', [PaymentController::class, 'store']);   

// API untuk ambil detail pembayaran (JSON)
Route::get('/payments/{order_id}', [PaymentController::class, 'show']);


// routes/api.php
Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return response()->json($request->user());
});


Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::put('/orders/{id}/status', [OrderController::class, 'adminUpdateOrderPaymentStatus']);
});