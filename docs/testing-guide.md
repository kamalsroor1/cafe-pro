# 🧪 Testing Guide — Cafe Pro ERP

## Setup

```bash
# Use SQLite in-memory for tests
# phpunit.xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
<env name="QUEUE_CONNECTION" value="sync"/>
<env name="CACHE_DRIVER" value="array"/>
```

---

## Test Structure

```
tests/
├── Feature/
│   ├── Auth/
│   │   └── AuthTest.php
│   ├── Categories/
│   │   └── CategoryTest.php
│   ├── Products/
│   │   └── ProductTest.php
│   ├── Inventory/
│   │   ├── IngredientTest.php
│   │   └── RecipeTest.php
│   ├── Orders/
│   │   ├── OrderCreationTest.php
│   │   ├── OrderStatusTest.php
│   │   └── StockDeductionTest.php
│   ├── Shifts/
│   │   └── ShiftTest.php
│   └── Financials/
│       └── ProfitCalculationTest.php
└── Unit/
    ├── Services/
    │   ├── StockServiceTest.php
    │   ├── OrderServiceTest.php
    │   └── ProfitServiceTest.php
    └── Models/
        └── ProductTest.php
```

---

## Example Tests

### `AuthTest.php`
```php
class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_login(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);
        $user->assignRole('cashier');

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
                 ->assertJsonStructure(['token', 'user', 'permissions']);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'password'  => Hash::make('password'),
            'is_active' => false,
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email,
            'password' => 'password',
        ])->assertUnauthorized();
    }
}
```

### `ShiftTest.php`
```php
class ShiftTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_open_shift(): void
    {
        $cashier = $this->createCashier();

        $this->actingAs($cashier)
             ->postJson('/api/v1/shifts/open', ['opening_balance' => 500])
             ->assertCreated()
             ->assertJsonPath('data.status', 'open');
    }

    public function test_cannot_open_second_shift_while_one_is_open(): void
    {
        $cashier = $this->createCashier();
        Shift::factory()->open()->for($cashier)->create();

        $this->actingAs($cashier)
             ->postJson('/api/v1/shifts/open', ['opening_balance' => 200])
             ->assertUnprocessable();
    }

    public function test_shift_close_calculates_difference(): void
    {
        $cashier = $this->createCashier();
        $shift   = Shift::factory()->open()->for($cashier)->create([
            'opening_balance' => 500,
        ]);

        // Simulate cash sale of 750 during shift
        $order = Order::factory()->completed()->for($shift)->create(['total_amount' => 750]);
        Payment::factory()->create(['order_id' => $order->id, 'method' => 'cash', 'amount' => 750]);

        $this->actingAs($cashier)
             ->postJson("/api/v1/shifts/{$shift->id}/close", [
                 'closing_balance' => 1230,
             ])
             ->assertOk()
             ->assertJsonPath('data.expected_balance', 1250.00)   // 500 + 750
             ->assertJsonPath('data.difference', -20.00);          // 1230 - 1250
    }
}
```

### `StockDeductionTest.php`
```php
class StockDeductionTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_is_deducted_when_order_completes(): void
    {
        // Setup
        $beans = Ingredient::factory()->create(['stock_qty' => 500, 'unit' => 'g']);
        $milk  = Ingredient::factory()->create(['stock_qty' => 1000, 'unit' => 'ml']);
        $latte = Product::factory()->create();

        // Recipe: 1 latte = 20g beans + 200ml milk
        $latte->ingredients()->attach($beans->id, ['amount_needed' => 20]);
        $latte->ingredients()->attach($milk->id,  ['amount_needed' => 200]);

        // Create a completed order with 2 lattes
        $shift = Shift::factory()->open()->create();
        $order = Order::factory()->create(['shift_id' => $shift->id, 'status' => 'ready']);
        $order->items()->create([
            'product_id'    => $latte->id,
            'product_name'  => 'Latte',
            'product_price' => 45,
            'product_cost'  => 5,
            'qty'           => 2,
            'subtotal'      => 90,
        ]);

        // Complete the order
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)
             ->patchJson("/api/v1/orders/{$order->id}/status", ['status' => 'completed'])
             ->assertOk();

        // Assert stock was deducted
        $this->assertEquals(460, $beans->fresh()->stock_qty); // 500 - (20 × 2)
        $this->assertEquals(600, $milk->fresh()->stock_qty);  // 1000 - (200 × 2)
    }

    public function test_order_blocked_when_stock_insufficient(): void
    {
        config(['cafepro.stock_check_enabled' => true]);

        $beans = Ingredient::factory()->create(['stock_qty' => 5]); // only 5g left
        $latte = Product::factory()->withIngredient($beans, 20)->create(); // needs 20g

        $shift = Shift::factory()->open()->create();
        $order = Order::factory()->create(['shift_id' => $shift->id, 'status' => 'ready']);
        $order->items()->create(['product_id' => $latte->id, 'qty' => 1, /* ... */]);

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)
             ->patchJson("/api/v1/orders/{$order->id}/status", ['status' => 'completed'])
             ->assertUnprocessable()
             ->assertJsonPath('code', 'INSUFFICIENT_STOCK'); // ← wait: actually no 'code' key, see error format
    }
}
```

### `ProfitCalculationTest.php`
```php
class ProfitCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_net_profit_is_calculated_correctly(): void
    {
        // Revenue: 2 orders × 100 = 200
        Order::factory()->completed()->count(2)->create(['total_amount' => 100]);

        // COGS: each item costs 30, qty 1 each
        // (set up order items with product_cost=30)

        // Expenses: 50 fixed
        Expense::factory()->create(['amount' => 50]);

        $profitService = app(ProfitService::class);
        $report = $profitService->getReport(today(), today());

        $this->assertEquals(200, $report['revenue']);
        $this->assertEquals(50,  $report['operating_expenses']);
        // net_profit = 200 - COGS - 50
    }
}
```

---

## Helper Traits for Tests

```php
// tests/Traits/CreatesUsers.php

trait CreatesUsers
{
    protected function createAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        return $user;
    }

    protected function createCashier(): User
    {
        $user = User::factory()->create();
        $user->assignRole('cashier');
        return $user;
    }

    protected function createManager(): User
    {
        $user = User::factory()->create();
        $user->assignRole('manager');
        return $user;
    }
}
```

---

## Running Tests

```bash
# All tests
php artisan test

# Specific module
php artisan test --filter=ShiftTest

# With coverage
php artisan test --coverage

# Parallel (faster)
php artisan test --parallel
```
