<?php


namespace App\Http\Controllers\Api;
use App\Models\ProductRecipe;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductRecipeController extends Controller
{
    // Ambil semua recipe
   public function index()
{
    $recipes = ProductRecipe::with('product:product_id,nama','rawMaterial:raw_material_id,nama')->get();
    return response()->json($recipes, 200, [], JSON_UNESCAPED_UNICODE);
}

    // Simpan recipe baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'raw_material_id' => 'required|exists:raw_materials,raw_material_id',
            'quantity' => 'required|numeric|min:0',
            'satuan' => 'nullable|string|max:50',
        ]);

        $recipe = ProductRecipe::create($validated);

        return response()->json([
            'message' => 'Recipe berhasil ditambahkan',
            'data' => $recipe
        ], 201);
    }

    // Lihat detail recipe tertentu
    public function show($id)
    {
        $recipe = ProductRecipe::with(['product', 'rawMaterial'])->findOrFail($id);
        return response()->json($recipe);
    }

    // Update recipe
    public function update(Request $request, $id)
    {
        $recipe = ProductRecipe::findOrFail($id);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'raw_material_id' => 'required|exists:raw_materials,raw_material_id',
            'quantity' => 'required|numeric|min:0',
            'satuan' => 'nullable|string|max:50',
        ]);

        $recipe->update($validated);

        return response()->json([
            'message' => 'Recipe berhasil diperbarui',
            'data' => $recipe
        ]);
    }

    // Hapus recipe
    public function destroy($id)
    {
        $recipe = ProductRecipe::findOrFail($id);
        $recipe->delete();

        return response()->json(['message' => 'Recipe berhasil dihapus'], 204);
    }
}
