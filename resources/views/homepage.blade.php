<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=3.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Halaman Utama</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @include('components.login-modal')
    @include('components.register-modal')
</head>


<body class="h-full w-full">
    {{-- pangil login modal --}}
    <x-login-modal />
    {{-- pangil signup modal --}}
    <x-register-modal />
    {{-- Pangil Profil Modal --}}
    <x-profil-modal />


    <div class="min-h-screen flex flex-col w-full">
        <!-- NAVBAR -->
        <div class="navbar bg-base-100 shadow-sm">
            <div class="flex-1">
                <a href="{{ url('/homepage') }}" class="btn btn-ghost text-4xl p-8">VYNTER&LUNE</a>
            </div>
            <div class="flex-none flex items-center gap-4">
                <!-- Notifikasi -->
                <!-- Notifikasi / Order History Dropdown -->
<div id="notifBtn" class="dropdown dropdown-end">
    <!-- Tombol ikon notifikasi -->
    <div id="notifToggle" tabindex="0" role="button" class="btn btn-ghost btn-circle">
        <div class="indicator">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 
                    6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 
                    6.165 6 8.388 6 11v3.159c0 .538-.214 
                    1.055-.595 1.436L4 17h5m6 0v1a3 3 0 
                    11-6 0v-1m6 0H9" />
            </svg>
            <!-- Badge jumlah order -->
            <span class="badge badge-sm indicator-item">
                <span id="totalOrders">0</span>
            </span>
        </div>
    </div>

    <!-- Konten dropdown -->
    <div id="notifDropdownContent" tabindex="0" class="card card-compact dropdown-content bg-base-100 z-1 mt-3 w-100 shadow">
        <div class="card-body">
            <!-- List riwayat order -->
            <div id="orderHistoryList" class="mt-2 space-y-2"></div>

            <!-- Tombol Show All -->
            <div class="card-actions">
                <button id="showAllOrdersBtn" class="btn btn-primary btn-block">Show All</button>
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




        <x-card-display />
        <x-show-product />


        {{-- fOOTER --}}
        <footer class="footer sm:footer-horizontal bg-neutral text-neutral-content p-10">
            <nav>
                <h6 class="footer-title">Services</h6>
                <a class="link link-hover">Branding</a>
                <a class="link link-hover">Design</a>
                <a class="link link-hover">Marketing</a>
                <a class="link link-hover">Advertisement</a>
            </nav>
            <nav>
                <h6 class="footer-title">Company</h6>
                <a class="link link-hover">About us</a>
                <a class="link link-hover">Contact</a>
                <a class="link link-hover">Jobs</a>
                <a class="link link-hover">Press kit</a>
            </nav>
            <nav>
                <h6 class="footer-title">Legal</h6>
                <a class="link link-hover">Terms of use</a>
                <a class="link link-hover">Privacy policy</a>
                <a class="link link-hover">Cookie policy</a>
            </nav>
        </footer>

    </div>


    {{-- Use @vite to load the specific scripts needed for this page --}}
    @vite([
        'resources/js/product.js',
        'resources/js/login.js',
        'resources/js/register.js',
        'resources/js/profil.js',
        'resources/js/logout.js',
        'resources/js/orderhistory.js',
    ])

</html>