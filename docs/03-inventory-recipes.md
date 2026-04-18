# 📦 Module 03 — Inventory & Recipe System (BOM)

## Overview

This module manages:
- **Raw Materials (Ingredients)**: Coffee beans, milk, sugar, etc.
- **Recipe (BOM)**: How much of each ingredient makes one product unit
- **Stock Deduction**: Automatically triggered when an order is completed
- **Wastage Logging**: Record expired or damaged stock as an expense

---

## Core Concept: Bill of Materials (BOM)

```
Product: Latte (1 cup)
├── Coffee Beans   → 20g
├── Milk           → 200ml
└── Sugar          → 5g

When 2 Lattes are sold:
├── Coffee Beans   → -40g  (stock_qty: 500g → 460g)
├── Milk           → -400ml
└── Sugar          → -10g
```

---

## Models

### `Ingredient` Model
```php
class Ingredient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'unit', 'stock_qty', 'min_stock_qty', 'cost_per_unit', 'supplier'
    ];

    protected $casts = [
        'stock_qty'     => 'decimal:3',
        'min_stock_qty' => 'decimal:3',
        'cost_per_unit' => 'decimal:4',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_ingredient')
                    ->withPivot('amount_needed')
                    ->withTimestamps();
    }

    public function wastageLogs(): HasMany
    {
        return $this->hasMany(WastageLog::class);
    }

    // True if stock is below minimum threshold
    public function isLowStock(): bool
    {
        return $this->stock_qty <= $this->min_stock_qty;
    }
}
```

### `WastageLog` Model
```php
class WastageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_id', 'shift_id', 'recorded_by',
        'qty_wasted', 'cost_value', 'reason', 'notes'
    ];

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }
}
```

---

## API Endpoints

### Ingredients

| Method | Endpoint | Permission | Description |
|---|---|---|---|
| GET | `/api/v1/ingredients` | view ingredients | List all with stock levels |
| POST | `/api/v1/ingredients` | manage ingredients | Create |
| GET | `/api/v1/ingredients/{id}` | view ingredients | Get detail with low-stock flag |
| PUT | `/api/v1/ingredients/{id}` | manage ingredients | Update |
| DELETE | `/api/v1/ingredients/{id}` | manage ingredients | Soft delete |
| POST | `/api/v1/ingredients/{id}/restock` | manage ingredients | Add stock |

### Recipes

| Method | Endpoint | Permission | Description |
|---|---|---|---|
| GET | `/api/v1/products/{id}/recipe` | view recipes | Get product recipe |
| POST | `/api/v1/products/{id}/recipe` | manage recipes | Set/update recipe |
| DELETE | `/api/v1/products/{id}/recipe/{ingredient_id}` | manage recipes | Remove ingredient from recipe |

### Wastage

| Method | Endpoint | Permission | Description |
|---|---|---|---|
| GET | `/api/v1/wastage` | view ingredients | List wastage logs |
| POST | `/api/v1/wastage` | log wastage | Log wastage event |

---

## Controller Spec: `IngredientController`

```php
class IngredientController extends Controller
{
    // GET /ingredients?low_stock=1
    public function index(Request $request): JsonResponse
    {
        $ingredients = Ingredient::query()
            ->when($request->low_stock, function ($q) {
                $q->whereColumn('stock_qty', '<=', 'min_stock_qty');
            })
            ->orderBy('name')
            ->paginate(30);

        return IngredientResource::collection($ingredients);
    }

    // POST /ingredients/{id}/restock
    public function restock(RestockIngredientRequest $request, Ingredient $ingredient): JsonResponse
    {
        $ingredient->increment('stock_qty', $request->qty);

        activity()
            ->performedOn($ingredient)
            ->log("Restocked {$request->qty} {$ingredient->unit}");

        return new IngredientResource($ingredient->fresh());
    }
}
```

---

## Controller Spec: `RecipeController`

```php
class RecipeController extends Controller
{
    // GET /products/{product}/recipe
    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'product'     => $product->only(['id', 'name']),
            'ingredients' => $product->ingredients->map(fn($ing) => [
                'ingredient_id' => $ing->id,
                'name'          => $ing->name,
                'unit'          => $ing->unit,
                'amount_needed' => $ing->pivot->amount_needed,
            ]),
            'calculated_cost' => $this->recipeService->calculateCost($product),
        ]);
    }

    // POST /products/{product}/recipe
    // Body: { ingredients: [{ingredient_id, amount_needed}] }
    public function update(UpdateRecipeRequest $request, Product $product): JsonResponse
    {
        // Sync the recipe
        $syncData = collect($request->ingredients)
            ->mapWithKeys(fn($item) => [
                $item['ingredient_id'] => ['amount_needed' => $item['amount_needed']]
            ])->all();

        $product->ingredients()->sync($syncData);

        // Recalculate and update product cost
        $cost = $this->recipeService->calculateCost($product->fresh());
        $product->update(['cost' => $cost]);

        return response()->json(['message' => 'Recipe updated.', 'new_cost' => $cost]);
    }
}
```

---

## Service Spec: `StockService`

```php
// app/Services/StockService.php

class StockService
{
    /**
     * Deduct stock for all items in a completed order.
     * Called by OrderService when order status → completed.
     */
    public function deductForOrder(Order $order): void
    {
        foreach ($order->items as $item) {
            $this->deductForProduct($item->product_id, $item->qty);
        }
    }

    /**
     * Deduct the required ingredients for N units of a product.
     */
    public function deductForProduct(int $productId, int $qty): void
    {
        $product = Product::with('ingredients')->findOrFail($productId);

        foreach ($product->ingredients as $ingredient) {
            $amountToDeduct = $ingredient->pivot->amount_needed * $qty;

            DB::transaction(function () use ($ingredient, $amountToDeduct) {
                $ingredient->decrement('stock_qty', $amountToDeduct);
            });
        }
    }

    /**
     * Check if all ingredients are available for an order.
     * Returns array of shortage details if any.
     */
    public function checkStockForOrder(Order $order): array
    {
        $shortages = [];

        foreach ($order->items as $item) {
            $product = Product::with('ingredients')->find($item->product_id);

            foreach ($product->ingredients as $ingredient) {
                $required = $ingredient->pivot->amount_needed * $item->qty;

                if ($ingredient->stock_qty < $required) {
                    $shortages[] = [
                        'ingredient'  => $ingredient->name,
                        'required'    => $required,
                        'available'   => $ingredient->stock_qty,
                        'unit'        => $ingredient->unit,
                        'shortage'    => $required - $ingredient->stock_qty,
                    ];
                }
            }
        }

        return $shortages;
    }

    /**
     * Log wastage and deduct from stock.
     */
    public function logWastage(array $data): WastageLog
    {
        $ingredient = Ingredient::findOrFail($data['ingredient_id']);
        $costValue  = $ingredient->cost_per_unit * $data['qty_wasted'];

        $log = WastageLog::create([
            ...$data,
            'cost_value' => $costValue,
        ]);

        $ingredient->decrement('stock_qty', $data['qty_wasted']);

        return $log;
    }
}
```

---

## Service Spec: `RecipeService`

```php
// app/Services/RecipeService.php

class RecipeService
{
    /**
     * Calculate total cost to produce 1 unit of a product.
     * Formula: SUM(ingredient.cost_per_unit × amount_needed)
     */
    public function calculateCost(Product $product): float
    {
        return $product->ingredients->reduce(function ($carry, $ingredient) {
            return $carry + ($ingredient->cost_per_unit * $ingredient->pivot->amount_needed);
        }, 0.0);
    }
}
```

---

## Request Specs

### `StoreIngredientRequest`
```php
public function rules(): array
{
    return [
        'name'          => ['required', 'string', 'max:255'],
        'unit'          => ['required', 'in:g,kg,ml,l,pcs,tbsp,tsp'],
        'stock_qty'     => ['required', 'numeric', 'min:0'],
        'min_stock_qty' => ['required', 'numeric', 'min:0'],
        'cost_per_unit' => ['required', 'numeric', 'min:0'],
        'supplier'      => ['nullable', 'string'],
    ];
}
```

### `UpdateRecipeRequest`
```php
public function rules(): array
{
    return [
        'ingredients'                  => ['required', 'array', 'min:1'],
        'ingredients.*.ingredient_id'  => ['required', 'exists:ingredients,id'],
        'ingredients.*.amount_needed'  => ['required', 'numeric', 'min:0.001'],
    ];
}
```

### `LogWastageRequest`
```php
public function rules(): array
{
    return [
        'ingredient_id' => ['required', 'exists:ingredients,id'],
        'qty_wasted'    => ['required', 'numeric', 'min:0.001'],
        'reason'        => ['required', 'in:expired,damaged,spillage,other'],
        'notes'         => ['nullable', 'string'],
        'shift_id'      => ['nullable', 'exists:shifts,id'],
    ];
}
```
