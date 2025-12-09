<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=3.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Detail Order History</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @include('components.login-modal')
    @include('components.register-modal')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.2.4/fabric.min.js"></script>
</head>


<body class="h-full w-full">
    {{-- pangil login modal --}}
    <x-login-modal />
    {{-- pangil signup modal --}}
    <x-register-modal />
    {{-- Pangil Profil Modal --}}
    <x-profil-modal />
    <div class="navbar bg-base-100 shadow-sm">
        <div class="flex-1 p-2">
            <a href="{{ url('/homepage') }}" class="btn btn-ghost text-4xl p-8 font-afternight"><span> <img
                        src="/img/logovynter.png" class="h-18 object-contain"></span> <a></a>

        </div>
        <div class="flex-none flex items-center gap-4">
            <!-- Notifikasi -->
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                    <div class="indicator">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 
                 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 
                 6.165 6 8.388 6 11v3.159c0 .538-.214 
                 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 
                 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="badge badge-sm indicator-item">3</span>
                    </div>
                </div>
                <div tabindex="0" class="card card-compact dropdown-content bg-base-100 z-1 mt-3 w-52 shadow">
                    <div class="card-body">
                        <span class="text-lg font-bold">3 Notifications</span>
                        <span class="text-info">You have new alerts</span>
                        <div class="card-actions">
                            <button class="btn btn-primary btn-block">View all</button>
                        </div>

                    </div>
                </div>
            </div>
            <!-- Nama User -->
            <span>|</span><span id="navbarUser" style="display:none" class="relative font-semibold text-lg px-2 cursor-pointer
                            after:content-[''] after:absolute after:left-1/2 after:-bottom-0.5
                            after:w-0 after:h-[2px] after:bg-indigo-500
                            after:transition-all after:duration-300 after:-translate-x-1/2
                            hover:after:w-full">
            </span>
            <button id="openLoginModal" class="btn btn-outline">Login</button>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- Back Button -->
        <button onclick="window.history.back()"
            class="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-6 group">
            <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="font-medium">Kembali</span>
        </button>

        <!-- Loading State -->
        <div id="loadingState" class="flex justify-center items-center py-20">
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600"></div>
        </div>

        <!-- Content Container -->
        <div id="contentContainer" class="hidden">
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Left Side - Order Details (60%) -->
                <div class="lg:w-[60%] space-y-6">
                    <!-- Order Status Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-800">Order #<span id="orderNumber"></span></h1>
                                <p class="text-sm text-gray-500 mt-1">Dibuat pada <span id="orderDate"></span></p>
                            </div>
                            <div id="statusBadge" class="px-4 py-2 rounded-full font-semibold text-sm"></div>
                        </div>

                        <!-- Timeline Progress -->
                        <div id="orderTimeline" class="relative">
                            <!-- Timeline will be inserted here -->
                        </div>
                    </div>

                    <!-- Product Details Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Detail Produk</h2>
                        <div class="flex gap-4">
                            <div class="w-32 h-32 flex-shrink-0 rounded-lg overflow-hidden border-2 border-gray-200">
                                <img id="productImage" src="" alt="Product" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1">
                                <h3 id="productName" class="text-lg font-semibold text-gray-800 mb-2"></h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Harga Satuan</span>
                                        <span id="productPrice" class="font-semibold"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Quantity</span>
                                        <span id="productQuantity" class="font-semibold"></span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t">
                                        <span class="text-gray-800 font-medium">Subtotal</span>
                                        <span id="productSubtotal" class="font-bold text-blue-600"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Image if exists -->
                        <div id="customImageContainer" class="mt-4 pt-4 border-t hidden">
                            <p class="text-sm font-medium text-gray-700 mb-2">Gambar Custom</p>
                            <img id="customImage" src="" alt="Custom"
                                class="w-48 h-48 object-cover rounded-lg border-2 border-gray-200">
                        </div>
                    </div>

                    <!-- Customer Info Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Informasi Pelanggan</h2>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500">Nama Lengkap</p>
                                    <p id="customerName" class="font-medium text-gray-800"></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500">Nomor Telepon</p>
                                    <p id="customerPhone" class="font-medium text-gray-800"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Alamat Pengiriman</h2>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <div class="flex-1">
                                <p id="shippingAddress" class="text-gray-800 leading-relaxed"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Summary (40%) -->
                <div class="lg:w-[40%]">
                    <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Ringkasan Biaya</h2>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Subtotal Produk</span>
                                <span id="summarySubtotal" class="font-semibold text-gray-800"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Biaya Admin</span>
                                <span id="summaryAdmin" class="font-semibold text-gray-800"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Biaya Pengiriman</span>
                                <span id="summaryShipping" class="font-semibold text-gray-800"></span>
                            </div>

                            <div class="border-t pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-gray-800">Total</span>
                                    <span id="summaryTotal" class="text-2xl font-bold text-blue-600"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Proof if exists -->
                        <div id="paymentProofContainer" class="mt-6 pt-6 border-t hidden">
                            <h3 class="font-semibold text-gray-800 mb-3">Bukti Pembayaran</h3>
                            <img id="paymentProof" src="" alt="Payment Proof"
                                class="w-50 rounded-lg border-2 border-gray-200 cursor-pointer hover:opacity-90 transition">
                        </div>

                        <!-- Action Buttons -->
                        <div id="actionButtons" class="mt-6 space-y-3">
                            <!-- Buttons will be inserted based on status -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error State -->
        <div id="errorState" class="hidden text-center py-20">
            <div class="text-6xl mb-4">⚠️</div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Terjadi Kesalahan</h2>
            <p class="text-gray-600 mb-6">Gagal memuat detail order</p>
            <button onclick="location.reload()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Coba Lagi
            </button>
        </div>
    </div>



    {{-- Use @vite to load the specific scripts needed for this page --}}
    @vite([
        'resources/js/login.js',
        'resources/js/register.js',
        'resources/js/profil.js',
        'resources/js/logout.js',
        'resources/js/orderhistorydetail.js',
    ])

</html>