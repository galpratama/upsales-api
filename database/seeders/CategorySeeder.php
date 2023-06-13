<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create "Food & Beverages" and "Clothing & Apparel" categories
        $categories = [
            [
                'name' => 'Food & Beverages',
            ],
            [
                'name' => 'Clothing & Apparel',
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
