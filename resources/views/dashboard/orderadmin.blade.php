@extends('layouts.sidebar')

@section('title', 'Order Detail')

@section('content')
    <h1 class="text-2xl font-bold mb-6 pl-7">Daftar Order</h1>

    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Customer</th>
                    <th>No. Telepon</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="orderBody">
                <!-- Data order akan diisi melalui orderadmin.js -->
            </tbody>
        </table>
    </div>



    <!-- Edit Order Modal -->
    <dialog id="editOrderModal" class="modal">
        <div class="modal-box p-8 max-w-md">
            <h3 class="font-bold text-xl text-center mb-6">Edit Status Order</h3>

            <form id="editOrderForm" class="space-y-4">
                <input type="hidden" id="editOrderId" name="order_id">

                <div>
                    <label class="block mb-1 font-medium">Status *</label>
                    <select id="editOrderStatus" name="status" class="select select-bordered w-full" required>
                        <option value="verified">Approve</option>
                        <option value="rejected">Reject</option>
                    </select>
                </div>

                <div class="modal-action flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="btn btn-outline">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary flex items-center">
                        <span id="editOrderSpinner" class="loading loading-spinner mr-2 hidden"></span>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>



    <!-- Modal Detail Order -->
    <div id="orderDetailModal" class="hidden fixed inset-0 bg-[rgba(0,0,0,0.25)] flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-11/12 max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Detail Order</h2>
                <button onclick="closeDetailModal()" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="flex gap-6">
                <!-- Kiri: Detail -->
                <div class="flex-1 grid grid-cols-3 gap-3 text-sm text-gray-700">
                    <div class="font-semibold">ID:</div>
                    <div class="col-span-2" id="detailOrderId"></div>

                    <div class="font-semibold">Nama:</div>
                    <div class="col-span-2" id="detailName"></div>

                    <div class="font-semibold">No. HP:</div>
                    <div class="col-span-2" id="detailPhone"></div>

                    <div class="font-semibold">Email:</div>
                    <div class="col-span-2" id="detailEmail"></div>

                    <div class="font-semibold">Produk:</div>
                    <div class="col-span-2" id="detailProduct"></div>

                    <div class="font-semibold">Jumlah:</div>
                    <div class="col-span-2" id="detailQty"></div>

                    <div class="font-semibold">Total:</div>
                    <div class="col-span-2" id="detailTotal"></div>

                    <div class="font-semibold">Tanggal:</div>
                    <div class="col-span-2" id="detailDate"></div>

                    <div class="font-semibold">Status:</div>
                    <div class="col-span-2" id="detailStatus"></div>

                    <div class="font-semibold">Alamat:</div>
                    <div class="col-span-2 break-words" id="detailAddress"></div>
                </div>

                <!-- Divider -->
                <div class="w-px bg-gray-300"></div>

                <!-- Kanan: Gambar -->
                <div class="flex flex-col gap-4 items-center">
                    <!-- Gambar Custom User -->
                    <div class="flex flex-col items-center">
                        <p class="text-xs font-semibold text-gray-700 mb-2">Custom Design</p>
                        <img id="detailProductImage" src="https://via.placeholder.com/150" alt="Custom Design"
                            class="w-36 h-36 object-cover rounded-lg shadow-md border" />

                        <!-- Tombol Download Custom Image -->
                        <button id="downloadCustomImageBtn"
                            class="mt-3 px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-400 shadow-sm text-xs transition hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </button>
                    </div>

                    <!-- Bukti Pembayaran -->
                    <div id="paymentProofContainer" class="flex flex-col items-center hidden">
                        <p class="text-xs font-semibold text-gray-700 mb-2">Bukti Pembayaran</p>
                        <img id="detailPaymentProof" src="https://via.placeholder.com/150" alt="Payment Proof"
                            class="w-36 h-36 object-cover rounded-lg shadow-md border" />

                        <!-- Tombol Download Bukti Pembayaran -->
                        <button id="downloadPaymentProofBtn"
                            class="mt-3 px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-400 shadow-sm text-xs transition hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 flex items-center justify-end gap-3">
                <button id="downloadSkuBtn"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-400 shadow-md text-sm transition">
                    Download SKU
                </button>

                <button onclick="closeDetailModal()"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 focus:ring-2 focus:ring-red-400 shadow-md text-sm transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>






    @vite([
        'resources/js/dashboard/orderadmin.js',
    ])
@endsection