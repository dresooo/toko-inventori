<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=3.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order</title>
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


    <div class="min-h-screen flex flex-col w-full">
        <!-- NAVBAR -->
        <div class="navbar bg-base-100 shadow-sm">
            <div class="flex-1 p-2">
                <a href="{{ url('/homepage') }}" class="btn btn-ghost text-4xl p-8 font-afternight"><span> <img
                            src="/img/logovynter.png" class="h-18 object-contain"></span> <a></a>

            </div>
            <div class="flex-none flex items-center gap-4">

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

        <div class="flex justify-center mt-10 min-h-screen bg-base-200">
            <div class="flex w-full max-w-7xl flex-col lg:flex-row items-start mt-0 mb-5 gap-5 px-5">
                <!-- Card kiri (70%) -->
                <div class="card bg-base-300 rounded-box basis-[60%] shadow-md">
                    <div class="card-body">
                        <h1 class="card-title mb-5 text-3xl">Pesan Product</h1>
                        <form id="orderForm" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            @auth
                                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            @endauth
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <!-- Nama Lengkap -->
                            <div>
                                <label class="label text-xl font-semibold mb-2">Nama Lengkap</label>
                                <input type="text" name="full_name" placeholder="Masukkan nama lengkap"
                                    class="input input-bordered w-full" required>
                            </div>

                            <!-- Nomor Telepon -->
                            <div>
                                <label class="label text-xl font-semibold mb-2">Nomor Telepon</label>
                                <input type="text" name="phone_number" placeholder="Contoh: 08123456789"
                                    class="input input-bordered w-full" required>
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label class="label text-xl font-semibold mb-2">Quantity</label>
                                <div class="flex items-center">
                                    <button type="button" id="decreaseQty" class="btn btn-square btn-outline">-</button>
                                    <input id="quantityInput" type="number" name="quantity" min="1" value="1"
                                        class="input input-bordered w-20 text-center mx-2" required>
                                    <button type="button" id="increaseQty" class="btn btn-square btn-outline">+</button>
                                </div>
                            </div>

                            <!-- Domisili -->
                            <div class="space-y-4">
                                <div>
                                    <label class="label text-lg font-medium mb-2">Provinsi</label>
                                    <select id="provinsi" name="provinsi" class="select select-bordered w-full"
                                        required>
                                        <option value="">Pilih Provinsi</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="label text-lg font-medium mb-2">Kota/Kabupaten</label>
                                    <select id="kota" name="kota" class="select select-bordered w-full" required>
                                        <option value="">Pilih Kota/Kabupaten</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="label text-lg font-medium mb-2">Kecamatan</label>
                                    <select id="kecamatan" name="kecamatan" class="select select-bordered w-full"
                                        required>
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="label text-lg font-medium mb-2">Kelurahan/Desa</label>
                                    <select id="kelurahan" name="kelurahan" class="select select-bordered w-full"
                                        required>
                                        <option value="">Pilih Kelurahan</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="label text-lg font-medium mb-2">Kode Pos</label>
                                    <input type="text" name="kode_pos" placeholder="Masukkan kode pos"
                                        class="input input-bordered w-full" required>
                                </div>

                                <!-- Alamat Lengkap -->
                                <div>
                                    <label class="label text-lg font-medium mb-2">Alamat Lengkap</label>
                                    <textarea name="shipping_addr" rows="3"
                                        placeholder="Masukkan alamat lengkap (jalan, nomor rumah, RT/RW)"
                                        class="textarea textarea-bordered w-full" required></textarea>
                                </div>
                            </div>

                            <!-- Custom Gambar -->
                            <input type="hidden" id="productType" value="{{ $product->product_type }}">
                            <div>
                                <label class="label text-xl font-semibold mb-2">Custom Gambar</label>
                                <input type="file" id="uploadImage" name="custom_gambar" accept=".jpg,.jpeg,.png"
                                    class="file-input file-input-bordered w-full">
                            </div>

                            <!-- Canvas preview -->
                            <div class="mt-4 flex flex-col items-center gap-4">
                                <canvas id="pinCanvas" width="300" height="300" class="border"></canvas>

                                <!-- Pilihan background PNG -->
                                <div class="flex gap-3">
                                    <button type="button" id="bgWhite"
                                        class="px-4 py-2 border rounded bg-white text-black shadow">
                                        Background Putih
                                    </button>
                                    <button type="button" id="bgBlack"
                                        class="px-4 py-2 border rounded bg-black text-white shadow">
                                        Background Hitam
                                    </button>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-6">
                                <button type="submit" class="btn btn-primary w-full">Submit Order</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Divider -->
                <div class="divider lg:divider-horizontal"></div>

                <!-- Card kanan (30%) -->
                <div class="card bg-base-300 rounded-box basis-[40%] shadow-md p-5 h-auto">
                    <!-- Bagian Atas: Gambar + Nama + Harga -->
                    <div class="flex items-center space-x-4 border-b pb-3">
                        <div class="w-24 h-24 flex-shrink-0">
                            <img src="{{ $product->gambar ? 'data:image/jpeg;base64,' . base64_encode($product->gambar) : 'https://via.placeholder.com/150' }}"
                                alt="{{ $product->nama }}"
                                class="rounded-lg object-cover w-full h-full border-2 border-white shadow-sm">
                        </div>
                        <div class="flex flex-col w-full">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-semibold max-w-[60%]">
                                    {{ $product->nama }}
                                </h2>
                                <p class="text-lg font-bold text-primary whitespace-nowrap" id="hargaProduk"
                                    data-harga="{{ $product->harga }}">
                                    Rp {{ number_format($product->harga, 0, ',', '.') }}
                                </p>
                            </div>
                            <!-- Tambahan stok tersedia pakai max_production -->
                            <p id="stokProduk" class="text-sm text-gray-500">Memuat stok...</p>
                        </div>
                    </div>

                    <!-- Bagian Bawah: Biaya -->
                    <div class="space-y-2 mt-3">
                        <div class="flex justify-between items-center">
                            <p class="text-base">Biaya Admin</p>
                            <span id="biaya-admin" class="font-semibold">Rp 5.000</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <p class="text-base">Biaya Pengiriman</p>
                            <span id="biaya-pengiriman" class="font-semibold">Rp 20.000</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <p class="text-base">Subtotal</p>
                            <span id="subtotal" class="font-semibold "></span>
                        </div>
                    </div>
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
        'resources/js/order.js',
        'resources/js/orderhistory.js',

    ])

</html>