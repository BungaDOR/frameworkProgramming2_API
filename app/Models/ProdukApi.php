<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukApi extends Model
{
    protected $table = 'produk_apis';

    protected $fillable = [
        'kodeBarang',
        'namaBarang',
        'harga',
        'stok',
        'deskripsi',
        'gambar',
        'kategori',
        'expiredDate',
        'rating',
    ];
}
