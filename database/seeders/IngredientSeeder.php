<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            [
                'name' => 'Coffee Beans',
                'unit' => 'kg',
                'stock_qty' => 10,
                'min_stock_qty' => 2,
                'cost_per_unit' => 200,
                'supplier' => 'Local Roastery',
            ],
            [
                'name' => 'Milk',
                'unit' => 'l',
                'stock_qty' => 20,
                'min_stock_qty' => 5,
                'cost_per_unit' => 25,
                'supplier' => 'Dairy Farm',
            ],
            [
                'name' => 'Sugar',
                'unit' => 'kg',
                'stock_qty' => 15,
                'min_stock_qty' => 3,
                'cost_per_unit' => 30,
                'supplier' => 'Wholesale Market',
            ],
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::firstOrCreate(['name' => $ingredient['name']], $ingredient);
        }
    }
}
