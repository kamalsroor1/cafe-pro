<?php

namespace Tests\Feature\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
        // Skip roles for now to focus on component logic, or add them if strictly enforced
        $this->actingAs($this->admin);
    }

    public function test_it_can_create_a_product(): void
    {
        $category = Category::factory()->create();

        Livewire::test('products.product-form')
            ->call('openModal')
            ->set('name', 'Latte')
            ->set('category_id', $category->id)
            ->set('price', 4.50)
            ->set('cost', 1.50)
            ->call('save')
            ->assertDispatched('productSaved');

        $this->assertDatabaseHas('products', [
            'name' => 'Latte',
            'price' => 4.50,
        ]);
    }

    public function test_it_can_update_a_product(): void
    {
        $product = Product::factory()->create(['name' => 'Old Name']);

        Livewire::test('products.product-form')
            ->call('openModal', ['product_id' => $product->id])
            ->set('name', 'New Name')
            ->call('save');

        $this->assertEquals('New Name', $product->fresh()->name);
    }

    public function test_it_can_delete_a_product(): void
    {
        $product = Product::factory()->create();

        Livewire::test('products.product-list')
            ->call('delete', $product->id);

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_it_validates_product_price(): void
    {
        $category = Category::factory()->create();

        Livewire::test('products.product-form')
            ->set('name', 'Latte')
            ->set('category_id', $category->id)
            ->set('price', -1) // Invalid
            ->call('save')
            ->assertHasErrors(['price' => 'min']);
    }
}
