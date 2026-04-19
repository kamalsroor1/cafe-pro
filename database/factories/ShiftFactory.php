<?php

namespace Database\Factories;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shift>
 */
class ShiftFactory extends Factory
{
    public function definition(): array
    {
        return [
            'started_by' => User::factory(),
            'starting_cash' => 100.00,
            'started_at' => now(),
            'status' => 'open',
        ];
    }
}
