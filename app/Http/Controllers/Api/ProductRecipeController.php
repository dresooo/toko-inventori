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
        'quantity_needed' => 'required|numeric|min:0',
    ]);

    //cek apakah ada duplikat
$exists = ProductRecipe::where('product_id', $validated['product_id'])
    ->where('raw_material_id', $validated['raw_material_id'])
    ->exists();

if ($exists) {
    return response()->json([
        'message' => 'Recipe untuk product dan raw material ini sudah ada, tidak boleh duplikat.'
    ], 422);
}
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
    // Update recipe
public function update(Request $request, $id)
{
    // Cari recipe berdasarkan ID
    $recipe = ProductRecipe::findOrFail($id);

    // Validasi hanya untuk raw_material_id dan quantity_needed
    $validated = $request->validate([
        'raw_material_id' => 'required|exists:raw_materials,raw_material_id',
        'quantity_needed' => 'required|numeric|min:0',
    ]);

    // Update data
    $recipe->update($validated);

    // Kembalikan response JSON
    return response()->json([
        'message' => 'Recipe berhasil diperbarui',
        'data' => $recipe
    ]);
}

public function getByProduct($productId)
{
    $recipes = ProductRecipe::where('product_id', $productId)->get();

    return response()->json($recipes, 200, [], JSON_UNESCAPED_UNICODE);
}
    public function destroy($id)
{
    $recipe = ProductRecipe::find($id);
    if (!$recipe) {
        return response()->json(['message' => 'Recipe tidak ditemukan'], 404);
    }

    try {
        $recipe->delete();
        return response()->json(['message' => 'Recipe berhasil dihapus'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Gagal menghapus recipe', 'error' => $e->getMessage()], 500);
    }
}


    public function updateByProductAndMaterial(Request $request)
{
    // Validasi
    $validated = $request->validate([
        'product_id' => 'required|exists:products,product_id',
        'raw_material_id' => 'required|exists:raw_materials,raw_material_id',
        'quantity_needed' => 'required|numeric|min:0',
    ]);

    // Cari recipe berdasarkan product_id + raw_material_id
    $recipe = ProductRecipe::where('product_id', $validated['product_id'])
                           ->where('raw_material_id', $validated['raw_material_id'])
                           ->first();

    if (!$recipe) {
        return response()->json([
            'message' => 'Recipe tidak ditemukan untuk kombinasi produk & bahan ini'
        ], 404);
    }

    // Update
    $recipe->update(['quantity_needed' => $validated['quantity_needed']]);

    return response()->json([
        'message' => 'Recipe berhasil diperbarui',
        'data' => $recipe
    ]);
}

//hapus product berdasarkan product id
public function destroyByProduct($productId)
{
    // Cari semua recipe dengan product_id tertentu
    $recipes = ProductRecipe::where('product_id', $productId)->get();

    if ($recipes->isEmpty()) {
        return response()->json(['message' => 'Tidak ada recipe untuk produk ini'], 404);
    }

    try {
        ProductRecipe::where('product_id', $productId)->delete();
        return response()->json(['message' => 'Semua recipe untuk produk berhasil dihapus'], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Gagal menghapus recipe',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
