<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'order_id',
        'amount',
        'payment_date',
        'payment_proof',
        'status',
    ];

    //ambil data order
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    
}