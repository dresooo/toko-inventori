<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Http\Controllers\Api\ProductController;


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