<?php

namespace Database\Factories;

use App\Models\RestaurantTable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RestaurantTable>
 */
class RestaurantTableFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'T' . $this->faker->unique()->numberBetween(1, 100),
            'capacity' => $this->faker->numberBetween(2, 8),
            'status' => 'available',
        ];
    }
}
