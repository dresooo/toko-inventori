<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Pastikan primary key pakai order_id
    protected $primaryKey = 'order_id';

    // Karena order_id auto increment integer
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'total_amount',
        'order_date',
        'status',
        'full_name',
        'phone_number',
        'shipping_addr',
        'custom_gambar',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
