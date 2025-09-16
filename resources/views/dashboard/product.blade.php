@extends('layouts.sidebar')

@section('title', 'Produk')

@section('content')
    <h1 class="text-2xl font-bold mb-6 pl-7">Daftar Produk</h1>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        {{-- <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-box text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Products</p>
                        <p class="text-2xl font-bold text-gray-900" id="totalProducts">-</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active</p>
                        <p class="text-2xl font-bold text-gray-900" id="activeProducts">-</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Low Stock</p>
                        <p class="text-2xl font-bold text-gray-900" id="lowStockProducts">-</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Out of Stock</p>
                        <p class="text-2xl font-bold text-gray-900" id="outOfStockProducts">-</p>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="flex justify-end">
            <!-- Tombol Tambah Produk -->
            <button type="button" id="openAddModalBtn" onclick="openAddModal()"
                class="flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 text-white 
                                                               hover:bg-blue-700 shadow-md focus:outline-none focus:ring-2 focus:ring-blue-400 mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span class="ml-2 font-medium">Tambah Produk</span>
            </button>
        </div>


        <!-- Products Grid -->
        <div id="productGrid" class="space-y-4">
            <!-- Loading skeleton -->
            <div id="loadingSkeleton" class="space-y-4"></div>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="hidden text-center py-12">
            <div class="max-w-md mx-auto">
                <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada produk</h3>
                <p class="text-gray-600 mb-6">Mulai dengan menambahkan produk pertama Anda.</p>
                <button onclick="openAddModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Product
                </button>
            </div>
        </div>
    </div>



    <!-- Add Product Modal -->
    <dialog id="addProductModal" class="modal">
        <div class="modal-box p-8 max-w-2xl">
            <!-- Header -->
            <h3 class="font-bold text-xl text-center mb-6">Tambah Produk</h3>

            <!-- Form -->
            <form id="addProductForm" class="space-y-4">
                <div>
                    <label class="block mb-1 font-medium">Nama Produk *</label>
                    <input type="text" name="nama" id="addProductName" placeholder="Nama produk" required
                        class="input input-bordered w-full" />
                </div>

                <div class="block mb-1 font-medium">
                    <div>
                        <label class="block mb-1 font-medium">Harga *</label>
                        <input type="number" name="harga" id="addProductPrice" placeholder="0" required min="0" step="0.01"
                            class="input input-bordered w-full" />
                    </div>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Deskripsi</label>
                    <textarea name="deskripsi" id="addProductDescription" rows="3" class="textarea textarea-bordered w-full"
                        placeholder="Deskripsi produk"></textarea>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Gambar Produk</label>
                    <input type="file" name="gambar" id="addProductImage" accept="image/*"
                        class="file-input file-input-bordered w-full" />
                    <div id="addImagePreview" class="mt-4 hidden">
                        <img id="addPreviewImg" src="" alt="Preview" class="h-32 w-auto rounded-lg border" />
                    </div>
                </div>

                <!-- Actions -->
                <div class="modal-action flex justify-end gap-2">
                    <button type="button" onclick="closeAddModal()" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-primary flex items-center">
                        <span id="addSubmitSpinner" class="loading loading-spinner mr-2 hidden"></span>
                        Simpan
                    </button>
                </div>
            </form>
        </div>

        <!-- Backdrop -->
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    {{-- modal untuk konfirmasi Hapus --}}
    <dialog id="deleteDialog" class="p-6 rounded-md w-96">
        <h2 class="text-lg font-semibold mb-4">Konfirmasi Hapus</h2>
        <p id="deleteDialogText" class="mb-4"></p>
        <div class="flex justify-end gap-3">
            <button id="cancelDelete" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
            <button id="confirmDeleteBtn" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
        </div>
    </dialog>

    <!-- Edit Product Modal -->
    <dialog id="editProductModal" class="modal">
        <div class="modal-box p-8 max-w-2xl">
            <!-- Header -->
            <h3 class="font-bold text-xl text-center mb-6">Edit Produk</h3>

            <!-- Form -->
            <form id="editProductForm" class="space-y-4">
                <input type="hidden" name="product_id" id="editProductId" />

                <div>
                    <label class="block mb-1 font-medium">Nama Produk *</label>
                    <input type="text" name="nama" id="editProductName" placeholder="Nama produk" required
                        class="input input-bordered w-full" />
                </div>

                <div class="block mb-1 font-medium">
                    <div>
                        <label class="block mb-1 font-medium">Harga *</label>
                        <input type="number" name="harga" id="editProductPrice" placeholder="0" required min="0" step="0.01"
                            class="input input-bordered w-full" />
                    </div>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Deskripsi</label>
                    <textarea name="deskripsi" id="editProductDescription" rows="3"
                        class="textarea textarea-bordered w-full" placeholder="Deskripsi produk"></textarea>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Gambar Produk</label>
                    <input type="file" name="gambar" id="editProductImage" accept="image/*"
                        class="file-input file-input-bordered w-full" />
                    <div id="editImagePreview" class="mt-4 hidden">
                        <img id="editPreviewImg" src="" alt="Preview" class="h-32 w-auto rounded-lg border" />
                    </div>
                </div>

                <!-- Actions -->
                <div class="modal-action flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-primary flex items-center">
                        <span id="editSubmitSpinner" class="loading loading-spinner mr-2 hidden"></span>
                        Simpan
                    </button>
                </div>
            </form>
        </div>

        <!-- Backdrop -->
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
    @vite([
        'resources/js/dashboard/productadmin.js',
    ])
@endsection