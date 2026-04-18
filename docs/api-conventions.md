# 🌐 API Conventions — Cafe Pro ERP

## Base URL

```
https://api.cafepro.com/api/v1/
```

---

## Authentication

All endpoints (except `/auth/login`) require a Sanctum Bearer token:

```http
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

---

## Response Format

### Success — Single Resource
```json
{
  "data": {
    "id": 1,
    "name": "Latte",
    "price": 45.00
  }
}
```

### Success — Collection (Paginated)
```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 98
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  }
}
```

### Success — Simple Message
```json
{
  "message": "Resource deleted successfully."
}
```

### Error — Validation (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "price": ["The price field is required."],
    "category_id": ["The selected category does not exist."]
  }
}
```

### Error — Auth (401)
```json
{
  "message": "Unauthenticated."
}
```

### Error — Permission (403)
```json
{
  "message": "This action is unauthorized."
}
```

### Error — Not Found (404)
```json
{
  "message": "No query results for model [App\\Models\\Product] 99"
}
```

### Error — Business Logic (422)
```json
{
  "message": "Insufficient stock for one or more ingredients.",
  "code": "INSUFFICIENT_STOCK",
  "shortages": [
    { "ingredient": "Coffee Beans", "required": 40, "available": 10, "unit": "g" }
  ]
}
```

---

## HTTP Status Codes Used

| Code | Meaning |
|---|---|
| 200 | Success (GET, PUT, PATCH) |
| 201 | Created (POST) |
| 204 | No Content (DELETE with no body) |
| 400 | Bad Request |
| 401 | Unauthenticated |
| 403 | Forbidden (no permission or shift lock) |
| 404 | Not Found |
| 422 | Unprocessable Entity (validation or business rule failure) |
| 500 | Server Error |

---

## Filtering & Sorting Conventions

```
GET /api/v1/orders?status=pending&shift_id=5&from=2024-01-01&to=2024-01-31
GET /api/v1/products?category_id=3&available=1
GET /api/v1/ingredients?low_stock=1
GET /api/v1/expenses?from=2024-01-01&to=2024-01-31&type=fixed

Sorting (optional):
GET /api/v1/products?sort_by=price&sort_dir=desc
```

---

## Pagination

All list endpoints return paginated results:
```
GET /api/v1/products?page=2&per_page=15
```

Default `per_page` is 20. Max is 100.

---

## Complete Route File Reference

```php
// routes/api.php

use App\Http\Controllers\Api\V1\{
    AuthController, UserController,
    CategoryController, ProductController, ProductAddonController,
    IngredientController, RecipeController, WastageController,
    ShiftController, TableController,
    OrderController, OrderItemController, PaymentController,
    ExpenseCategoryController, ExpenseController,
    ReportController, DashboardController,
    ReceiptController,
};

Route::prefix('v1')->group(function () {

    // ──────────────────────────────────────────────
    // PUBLIC
    // ──────────────────────────────────────────────
    Route::post('/auth/login', [AuthController::class, 'login']);

    // ──────────────────────────────────────────────
    // AUTHENTICATED
    // ──────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me',     [AuthController::class, 'me']);

        // Users (Admin only)
        Route::middleware('role:admin')->group(function () {
            Route::apiResource('users', UserController::class);
        });

        // Categories
        Route::middleware('permission:view categories')->group(function () {
            Route::get('categories',     [CategoryController::class, 'index']);
            Route::get('categories/{category}', [CategoryController::class, 'show']);
        });
        Route::middleware('permission:manage categories')->group(function () {
            Route::post('categories',    [CategoryController::class, 'store']);
            Route::put('categories/{category}', [CategoryController::class, 'update']);
            Route::delete('categories/{category}', [CategoryController::class, 'destroy']);
        });

        // Products
        Route::middleware('permission:view products')->group(function () {
            Route::get('products',       [ProductController::class, 'index']);
            Route::get('products/{product}', [ProductController::class, 'show']);
        });
        Route::middleware('permission:manage products')->group(function () {
            Route::post('products',      [ProductController::class, 'store']);
            Route::put('products/{product}', [ProductController::class, 'update']);
            Route::delete('products/{product}', [ProductController::class, 'destroy']);
        });

        // Ingredients
        Route::middleware('permission:view ingredients')->group(function () {
            Route::get('ingredients',    [IngredientController::class, 'index']);
            Route::get('ingredients/{ingredient}', [IngredientController::class, 'show']);
        });
        Route::middleware('permission:manage ingredients')->group(function () {
            Route::post('ingredients',   [IngredientController::class, 'store']);
            Route::put('ingredients/{ingredient}', [IngredientController::class, 'update']);
            Route::delete('ingredients/{ingredient}', [IngredientController::class, 'destroy']);
            Route::post('ingredients/{ingredient}/restock', [IngredientController::class, 'restock']);
        });

        // Recipes
        Route::get('products/{product}/recipe',  [RecipeController::class, 'show'])
             ->middleware('permission:view recipes');
        Route::post('products/{product}/recipe', [RecipeController::class, 'update'])
             ->middleware('permission:manage recipes');
        Route::delete('products/{product}/recipe/{ingredient}', [RecipeController::class, 'destroy'])
             ->middleware('permission:manage recipes');

        // Wastage
        Route::get('wastage',  [WastageController::class, 'index'])->middleware('permission:view ingredients');
        Route::post('wastage', [WastageController::class, 'store'])->middleware('permission:log wastage');

        // Tables
        Route::get('tables',         [TableController::class, 'index']);
        Route::middleware('role:admin|manager')->group(function () {
            Route::post('tables',    [TableController::class, 'store']);
            Route::put('tables/{table}', [TableController::class, 'update']);
            Route::delete('tables/{table}', [TableController::class, 'destroy']);
        });

        // Shifts
        Route::get('shifts',           [ShiftController::class, 'index'])->middleware('permission:view shifts');
        Route::get('shifts/active',    [ShiftController::class, 'active'])->middleware('permission:open shift');
        Route::post('shifts/open',     [ShiftController::class, 'open'])->middleware('permission:open shift');
        Route::post('shifts/{shift}/close',       [ShiftController::class, 'close'])->middleware('permission:close shift');
        Route::post('shifts/{shift}/force-close', [ShiftController::class, 'forceClose'])->middleware('role:admin');
        Route::get('shifts/{shift}',   [ShiftController::class, 'show'])->middleware('permission:view shifts');

        // Orders
        Route::middleware('permission:view orders')->group(function () {
            Route::get('orders',        [OrderController::class, 'index']);
            Route::get('orders/{order}', [OrderController::class, 'show']);
        });
        Route::middleware(['permission:create orders', 'shift.open'])->group(function () {
            Route::post('orders',       [OrderController::class, 'store']);
            Route::post('orders/{order}/items', [OrderItemController::class, 'store']);
            Route::delete('orders/{order}/items/{item}', [OrderItemController::class, 'destroy']);
        });
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])
             ->middleware('permission:update order status');
        Route::post('orders/{order}/cancel',  [OrderController::class, 'cancel'])
             ->middleware('permission:cancel orders');

        // Payments
        Route::post('orders/{order}/payment', [PaymentController::class, 'store'])
             ->middleware(['permission:process payments', 'shift.open']);

        // Receipts
        Route::get('orders/{order}/receipt',     [ReceiptController::class, 'show']);
        Route::get('orders/{order}/receipt/pdf', [ReceiptController::class, 'pdf']);

        // Expenses
        Route::middleware('permission:view expenses')->group(function () {
            Route::get('expenses',        [ExpenseController::class, 'index']);
            Route::get('expenses/{expense}', [ExpenseController::class, 'show']);
            Route::get('expense-categories', [ExpenseCategoryController::class, 'index']);
        });
        Route::middleware('permission:manage expenses')->group(function () {
            Route::post('expenses',       [ExpenseController::class, 'store']);
            Route::put('expenses/{expense}', [ExpenseController::class, 'update']);
            Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy']);
        });

        // Reports & Dashboard
        Route::middleware('permission:view reports')->group(function () {
            Route::get('reports/profit',   [ReportController::class, 'profit']);
            Route::get('reports/sales',    [ReportController::class, 'sales']);
            Route::get('reports/expenses', [ReportController::class, 'expenses']);
            Route::get('reports/cogs',     [ReportController::class, 'cogs']);
            Route::get('reports/stock',    [ReportController::class, 'stock']);
            Route::get('reports/shifts',   [ReportController::class, 'shifts']);
            Route::get('dashboard',        [DashboardController::class, 'index']);
        });
    });
});
```
