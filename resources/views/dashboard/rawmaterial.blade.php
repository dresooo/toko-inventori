@extends('layouts.sidebar')

@section('title', 'Raw Material')

@section('content')
    <h1 class="text-2xl font-bold mb-6 pl-7">Daftar Raw Material</h1>
    <div class="flex justify-end">
        <!-- Tombol Tambah Produk -->
        <button type="button" id="openAddRawMaterialModalBtn" onclick="openAddModal()"
            class="flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 text-white 
                                                                                                                                                                                                           hover:bg-blue-700 shadow-md focus:outline-none focus:ring-2 focus:ring-blue-400 mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span class="ml-2 font-medium">Tambah Material</span>
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Stok</th>
                    <th>Min-Stok</th>
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



    <!-- Add Raw Material Modal -->
    <dialog id="addRawMaterialModal" class="modal">
        <div class="modal-box p-8 max-w-2xl">
            <!-- Header -->
            <h3 class="font-bold text-xl text-center mb-6">Tambah Raw Material</h3>

            <!-- Form -->
            <form id="addRawMaterialForm" class="space-y-4">
                <!-- Nama -->
                <div>
                    <label class="block mb-1 font-medium">Nama Material *</label>
                    <input type="text" name="nama" id="addRawMaterialName" placeholder="Nama raw material" required
                        class="input input-bordered w-full" />
                </div>

                <!-- Stok & Satuan -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Jumlah Stok *</label>
                        <input type="number" name="stock_quantity" id="addRawMaterialStock" placeholder="0" required min="0"
                            class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Satuan *</label>
                        <input type="text" name="satuan" id="addRawMaterialUnit" placeholder="Contoh: kg, liter, pcs"
                            required class="input input-bordered w-full" />
                    </div>
                </div>

                <!-- Harga Beli -->
                <div>
                    <label class="block mb-1 font-medium">Harga Beli *</label>
                    <input type="number" name="harga_beli" id="addRawMaterialPrice" placeholder="0" required min="0"
                        step="0.01" class="input input-bordered w-full" />
                </div>
                <!-- Minimum Stock -->
                <div>
                    <label class="block mb-1 font-medium">Minimum Stock *</label>
                    <input type="number" name="minstock" id="addRawMaterialminstock" placeholder="0" required min="0"
                        step="0.01" class="input input-bordered w-full" />
                </div>
                <!-- Actions -->
                <div class="modal-action flex justify-end gap-2">
                    <button type="button" onclick="closeAddModal()" class="btn btn-outline">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary flex items-center">
                        <span id="addRawMaterialSpinner" class="loading loading-spinner mr-2 hidden"></span>
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


    <!-- Edit Raw Material Modal -->
    <dialog id="editRawMaterialModal" class="modal">
        <div class="modal-box p-8 max-w-2xl">
            <!-- Header -->
            <h3 class="font-bold text-xl text-center mb-6">Edit Raw Material</h3>

            <!-- Form -->
            <form id="editRawMaterialForm" class="space-y-4">
                <!-- Hidden ID -->
                <input type="hidden" name="raw_material_id" id="editRawMaterialId" />

                <!-- Nama -->
                <div>
                    <label class="block mb-1 font-medium">Nama Material *</label>
                    <input type="text" name="nama" id="editRawMaterialName" placeholder="Nama raw material" required
                        class="input input-bordered w-full" />
                </div>

                <!-- Stok & Satuan -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Jumlah Stok *</label>
                        <input type="number" name="stock_quantity" id="editRawMaterialStock" placeholder="0" required
                            min="0" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Satuan *</label>
                        <input type="text" name="satuan" id="editRawMaterialUnit" placeholder="Contoh: kg, liter, pcs"
                            required maxlength="20" class="input input-bordered w-full" />
                    </div>
                </div>

                <!-- Harga Beli -->
                <div>
                    <label class="block mb-1 font-medium">Harga Beli *</label>
                    <input type="number" name="harga_beli" id="editRawMaterialPrice" placeholder="0" required min="0"
                        step="0.01" class="input input-bordered w-full" />
                </div>

                <!-- Minimum Stock -->
                <div>
                    <label class="block mb-1 font-medium">Minimum Stock *</label>
                    <input type="number" name="minimum_stock" id="editRawMaterialMinStock" placeholder="0" required min="0"
                        step="0.01" class="input input-bordered w-full" />
                </div>

                <!-- Actions -->
                <div class="modal-action flex justify-end gap-2">
                    <button type="button" onclick="closeEditRawMaterial()" class="btn btn-outline">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary flex items-center">
                        <span id="editRawSubmitSpinner" class="loading loading-spinner mr-2 hidden"></span>
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
        'resources/js/dashboard/rawmaterial.js',
    ])
@endsection