<?php

namespace App\Services;

use App\Models\Product;
use App\Models\WastageLog;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Check if there's enough stock for an order's products.
     * Returns an array of shortages. Empty array means enough stock.
     */
    public function checkStockForProducts(array $productIdsWithQuantities): array
    {
        $shortages = [];
        $ingredientNeeds = [];

        // Aggregate total ingredient needs for all products in the order
        foreach ($productIdsWithQuantities as $productId => $qty) {
            $product = Product::with('ingredients')->find($productId);
            if (!$product) continue;

            foreach ($product->ingredients as $ingredient) {
                $needed = $ingredient->pivot->amount_needed * $qty;
                if (!isset($ingredientNeeds[$ingredient->id])) {
                    $ingredientNeeds[$ingredient->id] = 0;
                }
                $ingredientNeeds[$ingredient->id] += $needed;
            }
        }

        // Check if we have enough stock
        foreach ($ingredientNeeds as $ingredientId => $totalNeeded) {
            $ingredient = Ingredient::find($ingredientId);
            if (!$ingredient || $ingredient->stock_qty < $totalNeeded) {
                $shortages[] = [
                    'ingredient' => $ingredient ? $ingredient->name : 'Unknown',
                    'needed' => $totalNeeded,
                    'available' => $ingredient ? $ingredient->stock_qty : 0,
                    'unit' => $ingredient ? $ingredient->unit : '',
                ];
            }
        }

        return $shortages;
    }

    /**
     * Deduct stock for given products. Assumes stock has already been checked.
     */
    public function deductForProducts(array $productIdsWithQuantities): void
    {
        DB::transaction(function () use ($productIdsWithQuantities) {
            $ingredientNeeds = [];

            foreach ($productIdsWithQuantities as $productId => $qty) {
                $product = Product::with('ingredients')->find($productId);
                if (!$product) continue;

                foreach ($product->ingredients as $ingredient) {
                    $needed = $ingredient->pivot->amount_needed * $qty;
                    if (!isset($ingredientNeeds[$ingredient->id])) {
                        $ingredientNeeds[$ingredient->id] = 0;
                    }
                    $ingredientNeeds[$ingredient->id] += $needed;
                }
            }

            foreach ($ingredientNeeds as $ingredientId => $totalNeeded) {
                $ingredient = Ingredient::find($ingredientId);
                if ($ingredient) {
                    $ingredient->decrement('stock_qty', $totalNeeded);
                }
            }
        });
    }

    /**
     * Log wastage and deduct from stock.
     */
    public function logWastage(array $data): WastageLog
    {
        return DB::transaction(function () use ($data) {
            $ingredient = Ingredient::findOrFail($data['ingredient_id']);
            
            $costValue = $ingredient->cost_per_unit * $data['qty_wasted'];

            $log = WastageLog::create([
                'ingredient_id' => $ingredient->id,
                'shift_id'      => $data['shift_id'] ?? null,
                'recorded_by'   => auth()->id(),
                'qty_wasted'    => $data['qty_wasted'],
                'cost_value'    => $costValue,
                'reason'        => $data['reason'],
                'notes'         => $data['notes'] ?? null,
            ]);

            $ingredient->decrement('stock_qty', $data['qty_wasted']);

            return $log;
        });
    }
}
