<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => $name = $this->faker->unique()->words(2, true),
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 5, 100),
            'cost' => $this->faker->randomFloat(2, 1, 50),
            'is_available' => true,
        ];
    }
}
