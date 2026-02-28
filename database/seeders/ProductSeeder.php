<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'category_id' => 1, // Makanan
                'name' => 'Nasi Goreng',
                'sku' => 'FOOD-001',
                'description' => 'Nasi goreng spesial dengan telur dan ayam',
                'price' => 15000,
                'stock' => 50,
                'is_active' => true,
            ],
            [
                'category_id' => 1, // Makanan
                'name' => 'Mie Goreng',
                'sku' => 'FOOD-002',
                'description' => 'Mie goreng spesial dengan telur dan sayuran',
                'price' => 12000,
                'stock' => 50,
                'is_active' => true,
            ],
            [
                'category_id' => 2, // Minuman
                'name' => 'Es Teh',
                'sku' => 'DRINK-001',
                'description' => 'Es teh manis segar',
                'price' => 5000,
                'stock' => 100,
                'is_active' => true,
            ],
            [
                'category_id' => 2, // Minuman
                'name' => 'Es Jeruk',
                'sku' => 'DRINK-002',
                'description' => 'Es jeruk segar',
                'price' => 6000,
                'stock' => 100,
                'is_active' => true,
            ],
            [
                'category_id' => 3, // Snack
                'name' => 'Keripik Kentang',
                'sku' => 'SNACK-001',
                'description' => 'Keripik kentang renyah',
                'price' => 8000,
                'stock' => 30,
                'is_active' => true,
            ],
            [
                'category_id' => 4, // Alat Tulis
                'name' => 'Pulpen',
                'sku' => 'STAT-001',
                'description' => 'Pulpen hitam',
                'price' => 3000,
                'stock' => 200,
                'is_active' => true,
            ],
            [
                'category_id' => 5, // Elektronik
                'name' => 'Kabel USB',
                'sku' => 'ELEC-001',
                'description' => 'Kabel USB Type-C 1 meter',
                'price' => 25000,
                'stock' => 20,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}