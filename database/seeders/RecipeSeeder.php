<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Ingredient;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $latte = Product::where('name', 'Latte')->first();
        $espresso = Product::where('name', 'Espresso')->first();

        $beans = Ingredient::where('name', 'Coffee Beans')->first();
        $milk = Ingredient::where('name', 'Milk')->first();
        $sugar = Ingredient::where('name', 'Sugar')->first();

        if ($latte && $beans && $milk) {
            $latte->ingredients()->sync([
                $beans->id => ['amount_needed' => 0.018], // 18g
                $milk->id => ['amount_needed' => 0.200],  // 200ml
                $sugar->id => ['amount_needed' => 0.010], // 10g
            ]);
        }

        if ($espresso && $beans) {
            $espresso->ingredients()->sync([
                $beans->id => ['amount_needed' => 0.018], // 18g
            ]);
        }
    }
}
