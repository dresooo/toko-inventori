<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=3.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Halaman Utama</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- kirim token CSRF KE server --}}
    {{-- melindungi semua request POST/PUT/PATCH/DELETE dengan CSRF (Cross Site Request Forgery) token. --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
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


    <div class="min-h-screen flex flex-col w-full">
        <!-- NAVBAR -->
        <div class="navbar bg-base-100 shadow-sm">
            <div class="flex-1">
                <a class="btn btn-ghost text-4xl p-8">VYNTER&LUNE</a>
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

        <div class="flex justify-center mt-10 min-h-screen bg-base-200" x-data="{ step: 'choose', bank: '' }">
            <div class="flex w-full max-w-7xl flex-col lg:flex-row items-start mb-5 gap-5 px-5">

                <!-- Card kiri (Info pembayaran) -->
                <div class="card bg-base-300 rounded-box basis-[50%] shadow-md p-12">

                    <!-- Total Pembayaran -->
                    <div class="text-center mb-8" x-show="step === 'choose'">
                        <h2 class="text-3xl mb-2">Total Pembayaran</h2>
                        <p class="text-4xl text-black">{{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    </div>

                    <!-- Pilihan Bank -->
                    <template x-if="step === 'choose'">
                        <div>
                            <h3 class="text-xl font-semibold mb-4">Pilih Metode Pembayaran</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <!-- BCA -->
                                <div class="card cursor-pointer border p-6 text-center hover:border-primary transition rounded-lg"
                                    @click="bank = 'bca'; step = 'detail'">
                                    <div class="flex items-center justify-center h-28">
                                        <img src="/img/bcalogo.png" alt="BCA" class="h-full object-contain">
                                    </div>
                                </div>

                                <!-- Mandiri -->
                                <div class="card cursor-pointer border p-6 text-center hover:border-primary transition rounded-lg"
                                    @click="bank = 'mandiri'; step = 'detail'">
                                    <div class="flex items-center justify-center h-28">
                                        <img src="/img/mandirilogo.png" alt="Mandiri" class="h-full object-contain">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Detail Bank + Form Upload -->
                    <template x-if="step === 'detail'">
                        <div>
                            <!-- Info Bank dalam box -->
                            <div class="border rounded-lg p-6 mb-6 bg-base-100 shadow-sm">
                                <div class="flex justify-between items-center">
                                    <!-- Bagian teks -->
                                    <div class="space-y-6">
                                        <!-- Virtual Account Number -->
                                        <div>
                                            <p class="text-sm text-gray-500">Virtual Account Number</p>
                                            <p class="text-2xl font-bold"
                                                x-text="bank === 'bca' ? '8930162227400670' : '8930162227400680'"></p>
                                        </div>

                                        <!-- Virtual Account Name -->
                                        <div>
                                            <p class="text-sm text-gray-500">Virtual Account Name</p>
                                            <p class="text-xl">Mettania</p>
                                        </div>

                                        <!-- Amount to Pay -->
                                        <div>
                                            <p class="text-sm text-gray-500">Amount to Pay</p>
                                            <p class="text-2xl font-bold text-primary">
                                                IDR {{ number_format($order->total_amount, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Bagian logo -->
                                    <div class="flex items-center ml-8">
                                        <img x-show="bank === 'bca'" src="/img/bcalogo.png" alt="BCA"
                                            class="h-20 object-contain">
                                        <img x-show="bank === 'mandiri'" src="/img/mandirilogo.png" alt="Mandiri"
                                            class="h-20 object-contain">
                                    </div>
                                </div>
                            </div>

                            <!-- Form Upload Bukti -->
                            <form id="paymentForm" action="{{ route('payments.storeWeb') }}" method="POST"
                                enctype="multipart/form-data" class="space-y-4" x-data="{ loading: false }"
                                @submit.prevent="loading = true; $el.submit()">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                                <input type="hidden" name="amount" value="{{ $order->total_amount }}">
                                <input type="hidden" name="bank" :value="bank">

                                <div>
                                    <label class="block mb-2 font-semibold">Upload Bukti Pembayaran</label>
                                    <input type="file" name="payment_proof"
                                        class="file-input file-input-bordered w-full" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-full">Kirim Pembayaran</button>

                                <!-- Loading Spinner -->
                                <div x-show="loading"
                                    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                                    <div
                                        class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-24 w-24">
                                    </div>
                                </div>
                            </form>

                            <!-- Loader CSS -->
                            <style>
                                .loader {
                                    border-top-color: #3490dc;
                                    animation: spin 1s linear infinite;
                                }

                                @keyframes spin {
                                    0% {
                                        transform: rotate(0deg);
                                    }

                                    100% {
                                        transform: rotate(360deg);
                                    }
                                }
                            </style>

                            <!-- Back Button -->
                            <button class="btn btn-outline w-full mt-4" @click="step = 'choose'; bank = ''">
                                ← Kembali
                            </button>
                        </div>
                    </template>

                </div>

                <!-- Divider -->
                <div class="divider lg:divider-horizontal"></div>

                <!-- Card kanan (Order Summary) -->
                <div class="card bg-base-300 rounded-box basis-[50%] shadow-md p-12">
                    <h2 class="text-3xl font-bold mb-2">Order Summary</h2>
                    <p class="mb-2"><span class="font-semibold">Invoice #:</span> {{ $order->order_id }}</p>
                    <hr class="my-2 border-gray-400 mb-4">

                    <div class="mb-4 border-b border-gray-400 pb-2">
                        <p class="font-semibold">{{ $order->product->nama }}</p>
                        <p>{{ $order->quantity }} × Rp {{ number_format($order->product->harga, 0, ',', '.') }}</p>
                        <div class="flex justify-between font-bold mb-2">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($order->product->harga * $order->quantity, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @php
                        $subtotal = $order->product->harga * $order->quantity;
                        $adminFee = 5000;
                        $shippingFee = 20000;
                        $total = $subtotal + $adminFee + $shippingFee;
                    @endphp

                    <div class="space-y-1">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Biaya Admin</span>
                            <span>Rp {{ number_format($adminFee, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Biaya Pengiriman</span>
                            <span>Rp {{ number_format($shippingFee, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg mt-2">
                            <span>Total Amount Due</span>
                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div x-data="{ open: {{ session('payment_success') ? 'true' : 'false' }} }" x-cloak>
            <!-- Overlay -->
            <div x-show="open" class="fixed inset-0 bg-black/50 z-40"></div>

            <!-- Modal -->
            <div x-show="open" class="fixed inset-0 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg p-8 w-96 text-center">
                    <h2 class="text-2xl font-bold mb-4">Pembayaran Berhasil!</h2>
                    <p class="mb-6">Terima kasih, pembayaran Anda berhasil dikirim dan menunggu verifikasi.</p>
                    <button @click="window.location.href='/homepage'" class="btn btn-primary w-full">OK</button>
                </div>
            </div>
        </div>



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
        'resources/js/payment.js',
    ])

</html>