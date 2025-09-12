<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

     protected $table = 'products'; //seluruh tabel
     protected $primaryKey = 'product_id'; // ganti kolom pk
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['nama', 'deskripsi', 'harga', 'gambar'];

    public function recipes()
{
    return $this->hasMany(\App\Models\ProductRecipe::class, 'product_id');
}
}

