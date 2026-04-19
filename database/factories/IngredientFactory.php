<?php

namespace Database\Factories;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ingredient>
 */
class IngredientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'unit' => $this->faker->randomElement(['g', 'kg', 'ml', 'l', 'pcs', 'tbsp', 'tsp']),
            'stock_qty' => $this->faker->randomFloat(3, 10, 100),
            'min_stock_qty' => 5.00,
            'cost_per_unit' => $this->faker->randomFloat(4, 1, 50),
        ];
    }
}
