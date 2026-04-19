<?php

namespace Tests\Feature;

use App\Livewire\Pos\Terminal;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\RestaurantTable;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TerminalTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Shift $activeShift;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->activeShift = Shift::factory()->create([
            'started_by' => $this->user->id,
            'status' => 'open',
        ]);
    }

    public function test_it_initializes_with_correct_state(): void
    {
        Category::factory()->count(3)->create();
        RestaurantTable::factory()->count(5)->create();

        Livewire::test(Terminal::class)
            ->assertSet('posMode', 'tables')
            ->assertCount('tables', 5)
            ->assertCount('categories', 3);
    }

    public function test_it_can_open_a_table_and_transition_to_order_mode(): void
    {
        $table = RestaurantTable::factory()->create(['name' => 'T1']);

        Livewire::test(Terminal::class)
            ->call('openTable', $table->id)
            ->assertSet('posMode', 'order')
            ->assertSet('selectedTable.id', $table->id);
    }

    public function test_it_can_add_products_to_cart_and_persists_as_draft(): void
    {
        $table = RestaurantTable::factory()->create(['name' => 'T1']);
        $product = Product::factory()->create(['name' => 'Coffee', 'price' => 5.00]);

        Livewire::test(Terminal::class)
            ->call('openTable', $table->id)
            ->call('addToCart', $product->id)
            ->assertCount('cart', 1)
            ->assertSet('total', 5.00);

        // Verify database persistence (Draft Order)
        $this->assertDatabaseHas('orders', [
            'table_number' => 'T1',
            'status' => 'pending',
            'total' => 5.00,
        ]);

        $order = Order::where('table_number', 'T1')->first();
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }

    public function test_it_updates_existing_draft_when_adding_more_items(): void
    {
        $table = RestaurantTable::factory()->create(['name' => 'T1', 'status' => 'available']);
        $product = Product::factory()->create(['price' => 10.00]);

        $component = Livewire::test(Terminal::class)
            ->call('openTable', $table->id)
            ->call('addToCart', $product->id);

        $this->assertDatabaseCount('orders', 1);

        $component->call('addToCart', $product->id);

        $this->assertDatabaseCount('orders', 1); // Still 1 order
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_it_can_remove_items_from_cart_and_updates_database(): void
    {
        $table = RestaurantTable::factory()->create(['name' => 'T1']);
        $product = Product::factory()->create(['price' => 10.00]);

        Livewire::test(Terminal::class)
            ->call('openTable', $table->id)
            ->call('addToCart', $product->id)
            ->call('removeFromCart', 0)
            ->assertCount('cart', 0);

        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_it_can_update_item_quantity(): void
    {
        $table = RestaurantTable::factory()->create(['name' => 'T1']);
        $product = Product::factory()->create(['price' => 10.00]);

        Livewire::test(Terminal::class)
            ->call('openTable', $table->id)
            ->call('addToCart', $product->id)
            ->call('updateQuantity', 0, 1) // Add 1
            ->assertSet('cart.0.quantity', 2)
            ->call('updateQuantity', 0, -2) // Remove 2
            ->assertCount('cart', 0);

        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_it_prevents_checkout_with_empty_cart(): void
    {
        Livewire::test(Terminal::class)
            ->call('checkout')
            ->assertHasErrors(['cart' => 'Cart is empty']);
    }

    public function test_it_prevents_checkout_without_active_shift(): void
    {
        // Close the shift
        $this->activeShift->update(['status' => 'closed']);

        $product = Product::factory()->create();
        $table = RestaurantTable::factory()->create();

        Livewire::test(Terminal::class)
            ->call('openTable', $table->id)
            ->call('addToCart', $product->id)
            ->call('checkout')
            ->assertHasErrors(['shift' => 'No active shift. Please open a shift first.']);
    }

    public function test_it_completes_checkout_successfully(): void
    {
        $table = RestaurantTable::factory()->create(['name' => 'T1', 'status' => 'available']);
        $product = Product::factory()->create(['price' => 20.00]);

        Livewire::test(Terminal::class)
            ->call('openTable', $table->id)
            ->call('addToCart', $product->id)
            ->call('checkout')
            ->assertHasNoErrors()
            ->assertSet('posMode', 'tables')
            ->assertSet('cart', [])
            ->assertDispatched('toast-message');

        // Check the draft order was deleted
        $this->assertSoftDeleted('orders', [
            'table_number' => 'T1',
            'status' => 'pending',
        ]);

        // Check table status is back to available
        $this->assertEquals('available', $table->fresh()->status);

        // Check new order exists via service
        $this->assertDatabaseHas('orders', [
            'payment_status' => 'paid',
            'total' => 20.00,
        ]);
    }
}
