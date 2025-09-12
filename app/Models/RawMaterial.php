<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    protected $table = 'raw_materials';
    protected $primaryKey = 'raw_material_id'; // sesuai tabel
    public $timestamps = true;

    protected $fillable = [
        'nama',
        'deskripsi',
        'stock_quantity',
        'satuan',
        'harga_beli',
        'minimum_stock',
        'status'
    ];
}
