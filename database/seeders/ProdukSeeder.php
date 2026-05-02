<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProdukApi;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProdukApi::insert([
            [
                'kodeBarang' => 'BRG001',
                'namaBarang' => 'Laptop Gaming',
                'harga' => 15000000,
                'stok' => 5,
                'kategori' => 'Elektronik',
                'rating' => 4.5,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kodeBarang' => 'BRG002',
                'namaBarang' => 'Mouse Wireless',
                'harga' => 200000,
                'stok' => 20,
                'kategori' => 'Aksesoris',
                'rating' => 4.2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kodeBarang' => 'BRG003',
                'namaBarang' => 'Keyboard Mechanical',
                'harga' => 500000,
                'stok' => 15,
                'kategori' => 'Aksesoris',
                'rating' => 4.6,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kodeBarang' => 'BRG004',
                'namaBarang' => 'Monitor 24 Inch',
                'harga' => 2500000,
                'stok' => 8,
                'kategori' => 'Elektronik',
                'rating' => 4.3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kodeBarang' => 'BRG005',
                'namaBarang' => 'Flashdisk 64GB',
                'harga' => 100000,
                'stok' => 50,
                'kategori' => 'Storage',
                'rating' => 4.1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
