<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronics = Category::create(['title' => 'Электроника']);
        $clothes = Category::create(['title' => 'Одежда']);

        $apple = Brand::create(['title' => 'apple']);
        $nike = Brand::create(['title' => 'nike']);

        Product::create([
            'title' => 'iPhone 15 Pro',
            'description' => 'Флагманский смартфон',
            'price' => 999.99,
            'category_id' => $electronics->id,
            'brand_id' => $apple->id,
        ]);

        Product::create([
            'title' => 'Кроссовки Air Max',
            'description' => 'Спортивные кроссовки',
            'price' => 120.00,
            'category_id' => $clothes->id,
            'brand_id' => $nike->id,
        ]);
    }
}
