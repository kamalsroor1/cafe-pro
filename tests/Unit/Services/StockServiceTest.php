<?php

namespace Tests\Unit\Services;

use App\Models\Ingredient;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StockService $stockService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stockService = new StockService();
    }

    public function test_it_detects_stock_shortages(): void
    {
        $ingredient = Ingredient::factory()->create([
            'name' => 'Milk',
            'stock_qty' => 1.0, // 1 liter
            'unit' => 'l'
        ]);

        $product = Product::factory()->create();
        $product->ingredients()->attach($ingredient->id, ['amount_needed' => 0.5]); // Needs 0.5 per product

        // Requesting 1 product (0.5 needed, 1.0 available) -> No shortage
        $shortages = $this->stockService->checkStockForProducts([$product->id => 1]);
        $this->assertEmpty($shortages);

        // Requesting 3 products (1.5 needed, 1.0 available) -> Shortage
        $shortages = $this->stockService->checkStockForProducts([$product->id => 3]);
        $this->assertCount(1, $shortages);
        $this->assertEquals('Milk', $shortages[0]['ingredient']);
        $this->assertEquals(1.5, $shortages[0]['needed']);
    }

    public function test_it_deducts_stock_correctly(): void
    {
        $ingredient = Ingredient::factory()->create(['stock_qty' => 10.0]);
        $product = Product::factory()->create();
        $product->ingredients()->attach($ingredient->id, ['amount_needed' => 2.0]);

        $this->stockService->deductForProducts([$product->id => 3]);

        $this->assertEquals(4.0, $ingredient->fresh()->stock_qty); // 10 - (2 * 3) = 4
    }

    public function test_it_logs_wastage_and_deducts_stock(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $ingredient = Ingredient::factory()->create(['stock_qty' => 10.0, 'cost_per_unit' => 5.0]);

        $wastage = $this->stockService->logWastage([
            'ingredient_id' => $ingredient->id,
            'qty_wasted' => 2.0,
            'reason' => 'Expired',
            'notes' => 'Old milk',
        ]);

        $this->assertDatabaseHas('wastage_logs', [
            'id' => $wastage->id,
            'qty_wasted' => 2.0,
            'cost_value' => 10.0, // 2 * 5
        ]);

        $this->assertEquals(8.0, $ingredient->fresh()->stock_qty);
    }
}
