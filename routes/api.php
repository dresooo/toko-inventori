<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\RawMaterialController;

// Untuk API Product
Route::apiResource('products', ProductController::class);
// Edit / Update product
Route::put('/products/{id}', [ProductController::class, 'update']);
//Untuk Api Login Register
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

     // Update Profil Put
    Route::put('/user/{id}', [AuthController::class, 'update']);
});

Route::delete('/products/{id}', [ProductController::class, 'destroy']);



Route::get('/stocks', [StockController::class, 'index']);
Route::put('/stocks/{id}', [StockController::class, 'update']); 
Route::apiResource('raw-materials', RawMaterialController::class);