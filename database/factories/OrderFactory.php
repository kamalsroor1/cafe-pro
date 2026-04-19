<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'shift_id' => Shift::factory(),
            'cashier_id' => User::factory(),
            'type' => 'dine_in',
            'status' => 'pending',
            'subtotal' => 0,
            'tax' => 0,
            'total' => 0,
            'payment_status' => 'unpaid',
        ];
    }
}
