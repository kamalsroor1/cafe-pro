<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $hotDrinks = Category::where('name', 'Hot Drinks')->first();

        if ($hotDrinks) {
            Product::firstOrCreate([
                'name' => 'Latte',
                'slug' => Str::slug('Latte'),
            ], [
                'category_id' => $hotDrinks->id,
                'price' => 50,
                'cost' => 20,
            ]);

            Product::firstOrCreate([
                'name' => 'Espresso',
                'slug' => Str::slug('Espresso'),
            ], [
                'category_id' => $hotDrinks->id,
                'price' => 30,
                'cost' => 10,
            ]);
        }
    }
}
