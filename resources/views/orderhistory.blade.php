<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Order History</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- NAVBAR -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="/" class="text-3xl font-bold">VYNTER&LUNE</a>
            <div class="flex items-center gap-4">
                <button class="p-2 hover:bg-gray-100 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </button>
                <span id="navbarUser" class="font-semibold text-lg">User</span>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Back Button -->
        <button onclick="window.history.back()" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-6 group">
            <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
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
                            <img id="customImage" src="" alt="Custom" class="w-48 h-48 object-cover rounded-lg border-2 border-gray-200">
                        </div>
                    </div>

                    <!-- Customer Info Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Informasi Pelanggan</h2>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500">Nama Lengkap</p>
                                    <p id="customerName" class="font-medium text-gray-800"></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
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
                            <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
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
                            <img id="paymentProof" src="" alt="Payment Proof" class="w-full rounded-lg border-2 border-gray-200 cursor-pointer hover:opacity-90 transition">
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

    <script>
        // Get order ID from URL
        const pathParts = window.location.pathname.split('/');
        const orderId = pathParts[pathParts.length - 1];

        // Status translation and styling
        const statusConfig = {
            awaiting_payment: { 
                label: 'Menunggu Pembayaran', 
                class: 'bg-gradient-to-r from-yellow-400 to-orange-400 text-white',
                icon: '⏳'
            },
            pending: { 
                label: 'Menunggu Konfirmasi', 
                class: 'bg-gradient-to-r from-amber-400 to-yellow-500 text-white',
                icon: '⏰'
            },
            paid: { 
                label: 'Sudah Dibayar', 
                class: 'bg-gradient-to-r from-green-400 to-emerald-500 text-white',
                icon: '✅'
            },
            processing: { 
                label: 'Sedang Diproses', 
                class: 'bg-gradient-to-r from-blue-400 to-indigo-500 text-white',
                icon: '⚙️'
            },
            shipped: { 
                label: 'Sedang Dikirim', 
                class: 'bg-gradient-to-r from-indigo-400 to-purple-500 text-white',
                icon: '🚚'
            },
            delivered: { 
                label: 'Sudah Diterima', 
                class: 'bg-gradient-to-r from-green-500 to-teal-500 text-white',
                icon: '📦'
            },
            cancelled: { 
                label: 'Dibatalkan', 
                class: 'bg-gradient-to-r from-red-400 to-rose-500 text-white',
                icon: '❌'
            },
            verified: { 
                label: 'Terverifikasi', 
                class: 'bg-gradient-to-r from-emerald-500 to-green-600 text-white',
                icon: '✓'
            },
            rejected: { 
                label: 'Ditolak', 
                class: 'bg-gradient-to-r from-red-500 to-pink-600 text-white',
                icon: '⛔'
            }
        };

        // Format currency
        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Create timeline
        function createTimeline(status) {
            const stages = ['awaiting_payment', 'paid', 'processing', 'shipped', 'delivered'];
            const currentIndex = stages.indexOf(status);
            
            let html = '<div class="space-y-4">';
            stages.forEach((stage, index) => {
                const config = statusConfig[stage];
                const isActive = index <= currentIndex;
                const isCurrent = index === currentIndex;
                
                html += `
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center ${isActive ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-400'} ${isCurrent ? 'ring-4 ring-blue-200' : ''}">
                            ${isActive ? config.icon : '○'}
                        </div>
                        <div class="flex-1">
                            <p class="font-medium ${isActive ? 'text-gray-800' : 'text-gray-400'}">${config.label}</p>
                        </div>
                    </div>
                    ${index < stages.length - 1 ? '<div class="w-10 h-8 flex justify-center"><div class="w-0.5 h-full ' + (isActive ? 'bg-blue-600' : 'bg-gray-200') + '"></div></div>' : ''}
                `;
            });
            html += '</div>';
            
            return html;
        }

        // Load order details
        async function loadOrderDetail() {
            try {
                const token = localStorage.getItem('access_token');
                const response = await fetch(`/api/orders/${orderId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                if (!response.ok) throw new Error('Failed to fetch order');

                const data = await response.json();
                const order = data.data;

                // Hide loading, show content
                document.getElementById('loadingState').classList.add('hidden');
                document.getElementById('contentContainer').classList.remove('hidden');

                // Populate data
                const config = statusConfig[order.status];
                document.getElementById('orderNumber').textContent = order.order_id;
                document.getElementById('orderDate').textContent = new Date(order.created_at).toLocaleString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                const statusBadge = document.getElementById('statusBadge');
                statusBadge.textContent = config.icon + ' ' + config.label;
                statusBadge.className = `px-4 py-2 rounded-full font-semibold text-sm ${config.class}`;

                // Timeline
                document.getElementById('orderTimeline').innerHTML = createTimeline(order.status);

                // Product details
                document.getElementById('productImage').src = order.product.gambar || 'https://via.placeholder.com/150';
                document.getElementById('productName').textContent = order.product.nama;
                document.getElementById('productPrice').textContent = formatRupiah(order.product.harga);
                document.getElementById('productQuantity').textContent = order.quantity;
                document.getElementById('productSubtotal').textContent = formatRupiah(order.product.harga * order.quantity);

                // Custom image
                if (order.custom_gambar) {
                    document.getElementById('customImageContainer').classList.remove('hidden');
                    document.getElementById('customImage').src = order.custom_gambar;
                }

                // Customer info
                document.getElementById('customerName').textContent = order.full_name;
                document.getElementById('customerPhone').textContent = order.phone_number;

                // Shipping address
                const address = `${order.shipping_addr}, ${order.kelurahan}, ${order.kecamatan}, ${order.kota}, ${order.provinsi} ${order.kode_pos}`;
                document.getElementById('shippingAddress').textContent = address;

                // Summary
                const subtotal = order.product.harga * order.quantity;
                const admin = 5000;
                const shipping = 20000;
                const total = subtotal + admin + shipping;

                document.getElementById('summarySubtotal').textContent = formatRupiah(subtotal);
                document.getElementById('summaryAdmin').textContent = formatRupiah(admin);
                document.getElementById('summaryShipping').textContent = formatRupiah(shipping);
                document.getElementById('summaryTotal').textContent = formatRupiah(total);

                // Payment proof
                if (order.payment_proof) {
                    document.getElementById('paymentProofContainer').classList.remove('hidden');
                    document.getElementById('paymentProof').src = order.payment_proof;
                }

            } catch (error) {
                console.error('Error loading order:', error);
                document.getElementById('loadingState').classList.add('hidden');
                document.getElementById('errorState').classList.remove('hidden');
            }
        }

        // Load on page load
        loadOrderDetail();
    </script>
</body>
</html>