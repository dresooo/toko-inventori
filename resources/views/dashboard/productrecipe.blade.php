@extends('layouts.sidebar')

@section('title', 'Product Recipe')

@section('content')
    <h1 class="text-2xl font-bold mb-6 pl-7">Daftar Product Recipe</h1>
    <div class="flex justify-end">
        <!-- Tombol Tambah Recipe -->
        <button type="button" id="openrecipemodal" onclick="openRecipeModal()"
            class="flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 text-white 
                                                                                                            hover:bg-blue-700 shadow-md focus:outline-none focus:ring-2 focus:ring-blue-400 mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span class="ml-2 font-medium">Tambah Recipe</span>
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Produk</th>
                    <th>Raw Material</th>
                    <th>Dibuat Pada</th>
                    <th>Diperbarui Pada</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="recipeBody">
            </tbody>
        </table>
    </div>

    <!-- Add Product Recipe Modal -->
    <dialog id="addRecipeModal" class="modal">
        <div class="modal-box p-8 max-w-3xl">
            <!-- Header -->
            <h3 class="font-bold text-xl text-center mb-6">Tambah Product Recipe</h3>

            <!-- Form -->
            <form id="addRecipeForm" class="space-y-4">
                <!-- Dynamic Rows -->
                <div id="recipe-container" class="space-y-3">
                    <div class="recipe-item grid grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-1 font-medium">Product *</label>
                            <select name="product_id[]" id="productSelect" required
                                class="select select-bordered mt-2 w-full">
                                <option value="">-- Pilih Produk --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium">Raw Material ID *</label>
                            <select name="raw_material_id[]" id="rawMaterialSelect" required
                                class="select select-bordered mt-2 w-full">
                                <option value="">-- Pilih Raw Material --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium">Quantity Needed *</label>
                            <input type="number" name="quantity_needed[]" placeholder="0" required min="0"
                                class="input input-bordered mt-2 w-full" />
                        </div>
                    </div>
                </div>

                <!-- Tambah baris -->
                <button type="button" id="addRecipeRowBtn" class="btn btn-outline w-full mt-3">+ Tambah Baris</button>

                <!-- Actions -->
                <div class="modal-action flex justify-end gap-2">
                    <button type="button" onclick="closeRecipeModal()" class="btn btn-outline">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary flex items-center">
                        <span id="addRecipeSpinner" class="loading loading-spinner mr-2 hidden"></span>
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


    <!-- Edit Product Recipe Modal -->
    <dialog id="editRecipeModal" class="modal">
        <div class="modal-box p-8 max-w-3xl">
            <h3 class="font-bold text-xl text-center mb-6">Edit Product Recipe</h3>

            <form id="editRecipeForm" class="space-y-4">
                <!-- Dynamic Rows -->
                <div id="edit-recipe-container" class="space-y-3">
                    <!-- akan dimuat via JS -->
                </div>

                <!-- Actions -->
                <div class="modal-action flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="btn btn-outline">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary flex items-center">
                        <span id="editRecipeSpinner" class="loading loading-spinner mr-2 hidden"></span>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </dialog>




    @vite([
        'resources/js/dashboard/productrecipe.js',
    ])

@endsection