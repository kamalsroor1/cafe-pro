<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\User;
use App\Models\Shift;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\ShiftService;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemE2ETest extends TestCase
{
    use RefreshDatabase;

    public function test_full_business_workflow(): void
    {
        // 1. Setup - Admin User
        $admin = User::factory()->create();
        $this->actingAs($admin);

        // 2. Setup - Inventory (Milk and Coffee Beans)
        $milk = Ingredient::factory()->create(['name' => 'Milk', 'stock_qty' => 10, 'unit' => 'l', 'cost_per_unit' => 2]);
        $beans = Ingredient::factory()->create(['name' => 'Beans', 'stock_qty' => 5, 'unit' => 'kg', 'cost_per_unit' => 20]);

        $category = Category::factory()->create(['name' => 'Coffee']);
        $latte = Product::factory()->create(['name' => 'Latte', 'price' => 5.0, 'category_id' => $category->id]);
        
        // Recipe: 0.2L Milk, 0.02kg Beans
        $latte->ingredients()->attach($milk->id, ['amount_needed' => 0.2]);
        $latte->ingredients()->attach($beans->id, ['amount_needed' => 0.02]);

        // 3. Start Day - Open Shift
        $shiftService = app(ShiftService::class);
        $shift = $shiftService->openShift($admin, 100.0); // Start with $100

        $this->assertEquals(100.0, $shift->starting_cash);
        $this->assertEquals('open', $shift->status);

        // 4. Operations - Sell 2 Lattes
        $orderService = app(OrderService::class);
        $order = $orderService->createOrder([
            'type' => 'dine_in',
            'subtotal' => 10.0, // 2 * 5
            'tax' => 0,
            'total' => 10.0,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'items' => [
                ['product_id' => $latte->id, 'quantity' => 2, 'unit_price' => 5.0, 'subtotal' => 10.0]
            ]
        ]);

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'total' => 10.0, 'status' => 'pending']);
        
        // 5. Verify Stock Deduction
        // Original Milk: 10. Used: 2 * 0.2 = 0.4. Remaining: 9.6
        $this->assertEquals(9.6, $milk->fresh()->stock_qty);
        // Original Beans: 5. Used: 2 * 0.02 = 0.04. Remaining: 4.96
        $this->assertEquals(4.96, $beans->fresh()->stock_qty);

        // 6. End Day - Close Shift
        // Expected Cash: 100 (start) + 10 (sale) = 110
        $shiftService->closeShift($shift, 110.0);

        $shift->refresh();
        $this->assertEquals('closed', $shift->status);
        $this->assertEquals(110.0, $shift->expected_cash);
        $this->assertEquals(0, $shift->cash_difference);
    }
}
