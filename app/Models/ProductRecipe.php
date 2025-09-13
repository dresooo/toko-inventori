<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRecipe extends Model
{
    protected $primaryKey = 'recipe_id';
    protected $fillable = ['product_id','raw_material_id','quantity_needed'];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id', 'raw_material_id'); 
        // FK = raw_material_id, PK = raw_material_id
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
    
}
