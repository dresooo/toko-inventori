@extends('layouts.sidebar')

@section('title', 'Dashboard Utama')

@section('content')
    <div id="dashboard" class="container-fluid py-8">

        {{-- Header dengan Gradient --}}
        <div class="mb-8">
            <h1
                class="text-4xl md:text-5xl font-bold mb-2 bg-gradient-to-r from-purple-600 to-purple-900 bg-clip-text text-transparent">
                Dashboard Utama
            </h1>
            <p class="text-gray-500 text-sm md:text-base">Selamat datang! Berikut ringkasan bisnis Anda hari ini.</p>
        </div>

        {{-- 1️⃣ Statistik Utama dengan Icon & Animasi --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Pendapatan -->
            <div
                class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 overflow-hidden">
                <div class="p-6 text-white">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h6 class="text-purple-100 text-sm font-normal mb-2">Total Pendapatan</h6>
                            <h3 id="totalRevenue" class="text-3xl font-bold">Rp 0</h3>
                        </div>
                        <div class="text-purple-200 opacity-75">
                            <i class="fas fa-money-bill-wave text-4xl"></i>
                        </div>
                    </div>
                    <div class="text-purple-100 text-xs">
                        <i class="fas fa-arrow-up mr-1"></i>Keseluruhan pendapatan
                    </div>
                </div>
            </div>

            <!-- Total Order -->
            <div
                class="bg-gradient-to-br from-pink-500 to-red-500 rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 overflow-hidden">
                <div class="p-6 text-white">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h6 class="text-pink-100 text-sm font-normal mb-2">Total Order</h6>
                            <h3 id="totalOrders" class="text-3xl font-bold">0</h3>
                        </div>
                        <div class="text-pink-200 opacity-75">
                            <i class="fas fa-shopping-cart text-4xl"></i>
                        </div>
                    </div>
                    <div class="text-pink-100 text-xs">
                        <i class="fas fa-box mr-1"></i>Semua pesanan
                    </div>
                </div>
            </div>

            <!-- Produk Terjual -->
            <div
                class="bg-gradient-to-br from-blue-400 to-cyan-400 rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 overflow-hidden">
                <div class="p-6 text-white">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h6 class="text-blue-100 text-sm font-normal mb-2">Produk Terjual</h6>
                            <h3 id="totalProductsSold" class="text-3xl font-bold">0</h3>
                        </div>
                        <div class="text-blue-200 opacity-75">
                            <i class="fas fa-cubes text-4xl"></i>
                        </div>
                    </div>
                    <div class="text-blue-100 text-xs">
                        <i class="fas fa-chart-line mr-1"></i>Total unit terjual
                    </div>
                </div>
            </div>

            <!-- Rata-rata Order -->
            <div
                class="bg-gradient-to-br from-green-400 to-teal-400 rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 overflow-hidden">
                <div class="p-6 text-white">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h6 class="text-green-100 text-sm font-normal mb-2">Rata-rata Order</h6>
                            <h3 id="averageOrder" class="text-3xl font-bold">Rp 0</h3>
                        </div>
                        <div class="text-green-200 opacity-75">
                            <i class="fas fa-chart-pie text-4xl"></i>
                        </div>
                    </div>
                    <div class="text-green-100 text-xs">
                        <i class="fas fa-calculator mr-1"></i>Per transaksi
                    </div>
                </div>
            </div>
        </div>

        {{-- 2️⃣ Grafik Penjualan & Top Produk --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Grafik Penjualan -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg overflow-hidden animate-fadeInUp">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                        <div class="mb-3 sm:mb-0">
                            <h5 class="text-xl font-bold text-gray-800 mb-1">Penjualan per Bulan</h5>
                            <p class="text-gray-500 text-sm">Grafik performa penjualan bulanan</p>
                        </div>
                        <div class="flex gap-2">
                            <button
                                class="px-4 py-2 text-sm bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
                                Bulan
                            </button>
                            <button
                                class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                                Tahun
                            </button>
                        </div>
                    </div>
                    <div class="relative">
                        <canvas id="salesChart" style="height: 300px; max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            {{-- 3️⃣ Top Produk Terlaris --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden animate-fadeInUp">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h5 class="text-xl font-bold text-gray-800 mb-1">Produk Terlaris</h5>
                            <p class="text-gray-500 text-sm">Top 3 produk favorit</p>
                        </div>
                        <i class="fas fa-trophy text-yellow-500 text-2xl"></i>
                    </div>
                    <div id="topProducts" class="space-y-4"></div>
                </div>
            </div>
        </div>
        <button id="downloadPDF" class="btn btn-primary mb-4">
            Download Dashboard PDF
        </button>

    </div>

    <style>
        /* Animation untuk Card */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Smooth transitions untuk semua elemen interaktif */
        * {
            transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- html2canvas dan jsPDF -->
    <!-- html2canvas -->

    @vite([
        'resources/js/dashboard/dashboard.js',
    ])

@endsection