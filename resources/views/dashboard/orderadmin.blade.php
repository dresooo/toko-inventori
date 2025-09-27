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
                        <option value="pending">Pending</option>
                        <option value="processing">Diproses</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
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
    <!-- Modal Detail Order -->
    <div id="orderDetailModal" class="hidden fixed inset-0 bg-[rgba(0,0,0,0.25)] flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-11/12 max-w-3xl p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Detail Order</h2>
                <button onclick="closeDetailModal()" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body menggunakan grid 2 kolom -->
            <div class="grid grid-cols-3 gap-4 text-gray-700">
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

            <!-- Footer -->
            <div class="mt-6 flex justify-end">
                <button onclick="closeDetailModal()"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 shadow-md">
                    Tutup
                </button>
            </div>
        </div>
    </div>





    @vite([
        'resources/js/dashboard/orderadmin.js',
    ])
@endsection