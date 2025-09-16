@extends('layouts.sidebar')

@section('title', 'Raw Material')

@section('content')
    <h1 class="text-2xl font-bold mb-6 pl-7">Daftar Stock Produk</h1>

    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Nama Produk</th>
                    <th class="px-4 py-2">Max Production</th>
                </tr>
            </thead>
            <tbody id="materialStockBody">
                <!-- Data akan diisi melalui JS -->
            </tbody>
        </table>
    </div>

    @vite([
        'resources/js/dashboard/stock.js',
    ])
@endsection