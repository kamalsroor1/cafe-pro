<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'expense_category_id' => ExpenseCategory::factory(),
            'shift_id' => Shift::factory(),
            'recorded_by' => User::factory(),
            'amount' => $this->faker->randomFloat(2, 5, 200),
            'description' => $this->faker->sentence(),
            'expense_date' => now(),
            'payment_method' => 'cash',
        ];
    }
}
