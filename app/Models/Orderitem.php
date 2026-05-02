<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orderitem extends Model
{
    // protected $table = 'orderitems';
    use HasFactory;

    protected $fillable = [
        'order_id',
        'produk_id',
        'quantity',
        'price',
        'subtotal',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function produk()
    {
        return $this->belongsTo(ProdukApi::class, 'produk_id');
    }
}
