<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Makanan',
                'description' => 'Berbagai jenis makanan',
            ],
            [
                'name' => 'Minuman',
                'description' => 'Berbagai jenis minuman',
            ],
            [
                'name' => 'Snack',
                'description' => 'Berbagai jenis makanan ringan',
            ],
            [
                'name' => 'Alat Tulis',
                'description' => 'Berbagai jenis alat tulis',
            ],
            [
                'name' => 'Elektronik',
                'description' => 'Berbagai jenis barang elektronik',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}