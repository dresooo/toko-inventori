@extends('layouts.sidebar')

@section('title', 'Raw Material')

@section('content')
    <h1 class="text-2xl font-bold mb-6 pl-7">Daftar Raw Material</h1>

    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                    <th>Harga Beli</th>
                    <th>Dibuat Pada</th>
                    <th>Diperbarui Pada</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="materialBody">
            </tbody>
        </table>
    </div>

    @vite([
        'resources/js/dashboard/rawmaterial.js',
    ])
@endsection