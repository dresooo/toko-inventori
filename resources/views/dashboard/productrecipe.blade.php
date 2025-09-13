@extends('layouts.sidebar')

@section('title', 'Product Recipe')

@section('content')
    <h1 class="text-2xl font-bold mb-6 pl-7">Daftar Product Recipe</h1>
    <div class="flex justify-end">
        <!-- Tombol Tambah Recipe -->
        <button type="button" id="openAddRecipeModalBtn" onclick="openAddRecipeModal()" class="flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 text-white 
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
                    <th>Quantity Needed</th>
                    <th>Dibuat Pada</th>
                    <th>Diperbarui Pada</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="recipeBody">
            </tbody>
        </table>
    </div>

    <!-- Add Recipe Modal -->
    <dialog id="addRecipeModal" class="modal">
        <div class="modal-box p-8 max-w-2xl">
            <h3 class="font-bold text-xl text-center mb-6">Tambah Product Recipe</h3>

            <form id="addRecipeForm" class="space-y-4">
                <!-- Produk -->
                <div>
                    <label class="block mb-1 font-medium">Produk *</label>
                    <select name="product_id" id="addProductId" class="select select-bordered w-full" required>
                        <!-- opsi produk dari JS -->
                    </select>
                </div>

                <!-- Raw Material -->
                <div>
                    <label class="block mb-1 font-medium">Raw Material *</label>
                    <select name="raw_material_id" id="addRawMaterialId" class="select select-bordered w-full" required>
                        <!-- opsi raw material dari JS -->
                    </select>
                </div>

                <!-- Quantity -->
                <div>
                    <label class="block mb-1 font-medium">Quantity Needed *</label>
                    <input type="number" name="quantity_needed" id="addQuantityNeeded" placeholder="0" required min="0"
                        step="0.01" class="input input-bordered w-full" />
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block mb-1 font-medium">Catatan</label>
                    <input type="text" name="catatan" id="addCatatan" placeholder="Opsional"
                        class="input input-bordered w-full" />
                </div>

                <div class="modal-action flex justify-end gap-2">
                    <button type="button" onclick="closeAddRecipeModal()" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-primary flex items-center">
                        <span id="addRecipeSpinner" class="loading loading-spinner mr-2 hidden"></span>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Edit Recipe Modal -->
    <dialog id="editRecipeModal" class="modal">
        <div class="modal-box p-8 max-w-2xl">
            <h3 class="font-bold text-xl text-center mb-6">Edit Product Recipe</h3>

            <form id="editRecipeForm" class="space-y-4">
                <input type="hidden" name="recipe_id" id="editRecipeId" />

                <!-- Produk -->
                <div>
                    <label class="block mb-1 font-medium">Produk *</label>
                    <select name="product_id" id="editProductId" class="select select-bordered w-full" required>
                        <!-- opsi produk dari JS -->
                    </select>
                </div>

                <!-- Raw Material -->
                <div>
                    <label class="block mb-1 font-medium">Raw Material *</label>
                    <select name="raw_material_id" id="editRawMaterialId" class="select select-bordered w-full" required>
                        <!-- opsi raw material dari JS -->
                    </select>
                </div>

                <!-- Quantity -->
                <div>
                    <label class="block mb-1 font-medium">Quantity Needed *</label>
                    <input type="number" name="quantity_needed" id="editQuantityNeeded" placeholder="0" required min="0"
                        step="0.01" class="input input-bordered w-full" />
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block mb-1 font-medium">Catatan</label>
                    <input type="text" name="catatan" id="editCatatan" placeholder="Opsional"
                        class="input input-bordered w-full" />
                </div>

                <div class="modal-action flex justify-end gap-2">
                    <button type="button" onclick="closeEditRecipeModal()" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-primary flex items-center">
                        <span id="editRecipeSpinner" class="loading loading-spinner mr-2 hidden"></span>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    @vite([
        'resources/js/dashboard/productrecipe.js',
    ])

@endsection