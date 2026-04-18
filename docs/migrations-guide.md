# 📦 Migrations Guide — Cafe Pro ERP

> Run migrations in the exact order listed below to respect foreign key constraints.

---

## Migration Run Order

```
Phase 1 — Users & Auth
────────────────────────────────────────────────────
2024_01_01_000001_create_users_table.php
2024_01_01_000002_create_personal_access_tokens_table.php  (Sanctum)
2024_01_01_000003_create_permission_tables.php              (Spatie — run via: php artisan vendor:publish)

Phase 2 — Menu
────────────────────────────────────────────────────
2024_01_02_000001_create_categories_table.php
2024_01_02_000002_create_products_table.php
2024_01_02_000003_create_product_addons_table.php

Phase 3 — Inventory
────────────────────────────────────────────────────
2024_01_03_000001_create_ingredients_table.php
2024_01_03_000002_create_product_ingredient_table.php

Phase 4 — Operations
────────────────────────────────────────────────────
2024_01_04_000001_create_tables_table.php
2024_01_04_000002_create_shifts_table.php
2024_01_04_000003_create_orders_table.php
2024_01_04_000004_create_order_items_table.php
2024_01_04_000005_create_order_item_addons_table.php
2024_01_04_000006_create_payments_table.php

Phase 5 — Financials
────────────────────────────────────────────────────
2024_01_05_000001_create_expense_categories_table.php
2024_01_05_000002_create_expenses_table.php
2024_01_05_000003_create_wastage_logs_table.php

Phase 6 — Audit
────────────────────────────────────────────────────
2024_01_06_000001_create_activity_log_table.php             (Spatie ActivityLog)
```

---

## Migration Templates

### Pattern: Standard Table
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('table_name', function (Blueprint $table) {
            $table->id();
            // ... columns
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_name');
    }
};
```

### Pattern: Table with SoftDeletes
```php
$table->id();
// ... columns
$table->timestamps();
$table->softDeletes(); // adds deleted_at column
```

### Pattern: Enum Column
```php
$table->enum('status', ['pending', 'preparing', 'ready', 'completed', 'cancelled'])
      ->default('pending');
```

### Pattern: Decimal for Money
```php
$table->decimal('amount', 10, 2);        // Up to 99,999,999.99
$table->decimal('cost_per_unit', 10, 4); // More precision for ingredient costs
$table->decimal('qty', 10, 3);           // For ingredient quantities (grams, ml)
```

### Pattern: Nullable Foreign Key
```php
$table->foreignId('table_id')->nullable()->constrained('tables')->nullOnDelete();
```

### Pattern: Foreign Key with Restrict (financial history)
```php
// Don't delete products if they have order history
$table->foreignId('product_id')->constrained('products')->restrictOnDelete();
```

---

## Full Migration Code

### `create_categories_table`
```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
    $table->text('description')->nullable();
    $table->string('image')->nullable();
    $table->integer('sort_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### `create_products_table`
```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained('categories');
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('image')->nullable();
    $table->decimal('price', 10, 2);
    $table->decimal('cost', 10, 2)->default(0);
    $table->decimal('tax_rate', 5, 2)->default(0);
    $table->boolean('is_available')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
    $table->softDeletes();
});
```

### `create_ingredients_table`
```php
Schema::create('ingredients', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->enum('unit', ['g', 'kg', 'ml', 'l', 'pcs', 'tbsp', 'tsp']);
    $table->decimal('stock_qty', 10, 3)->default(0);
    $table->decimal('min_stock_qty', 10, 3)->default(0);
    $table->decimal('cost_per_unit', 10, 4);
    $table->string('supplier')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

### `create_product_ingredient_table`
```php
Schema::create('product_ingredient', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
    $table->foreignId('ingredient_id')->constrained('ingredients')->cascadeOnDelete();
    $table->decimal('amount_needed', 10, 3);
    $table->timestamps();

    $table->unique(['product_id', 'ingredient_id']);
});
```

### `create_shifts_table`
```php
Schema::create('shifts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users');
    $table->decimal('opening_balance', 10, 2);
    $table->decimal('closing_balance', 10, 2)->nullable();
    $table->decimal('expected_balance', 10, 2)->nullable();
    $table->decimal('difference', 10, 2)->nullable();
    $table->enum('status', ['open', 'closed'])->default('open');
    $table->text('notes')->nullable();
    $table->timestamp('opened_at');
    $table->timestamp('closed_at')->nullable();
    $table->timestamps();
});
```

### `create_orders_table`
```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('order_number')->unique();
    $table->foreignId('shift_id')->constrained('shifts');
    $table->foreignId('user_id')->constrained('users');
    $table->foreignId('table_id')->nullable()->constrained('tables')->nullOnDelete();
    $table->enum('type', ['dine_in', 'takeaway', 'delivery'])->default('dine_in');
    $table->enum('status', ['pending', 'preparing', 'ready', 'completed', 'cancelled'])->default('pending');
    $table->decimal('subtotal', 10, 2)->default(0);
    $table->decimal('tax_amount', 10, 2)->default(0);
    $table->decimal('discount_amount', 10, 2)->default(0);
    $table->decimal('total_amount', 10, 2)->default(0);
    $table->string('customer_name')->nullable();
    $table->string('customer_phone', 50)->nullable();
    $table->text('notes')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->timestamp('cancelled_at')->nullable();
    $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
    $table->text('cancel_reason')->nullable();
    $table->timestamps();
    $table->softDeletes();

    $table->index('shift_id');
    $table->index('status');
    $table->index('created_at');
});
```

### `create_order_items_table`
```php
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
    $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
    $table->string('product_name');       // Snapshot
    $table->decimal('product_price', 10, 2); // Snapshot
    $table->decimal('product_cost', 10, 2);  // Snapshot for COGS
    $table->integer('qty')->default(1);
    $table->decimal('subtotal', 10, 2);
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->index('order_id');
});
```

### `create_expenses_table`
```php
Schema::create('expenses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('expense_category_id')->constrained('expense_categories');
    $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
    $table->foreignId('recorded_by')->constrained('users');
    $table->decimal('amount', 10, 2);
    $table->text('description')->nullable();
    $table->date('date');
    $table->string('receipt_image')->nullable();
    $table->timestamps();

    $table->index('date');
    $table->index('shift_id');
});
```

---

## Seeder Order

```
DatabaseSeeder
├── RolesAndPermissionsSeeder   ← Must run first
├── AdminUserSeeder              ← Creates the default Admin user
├── CategorySeeder               ← Sample categories
├── ProductSeeder                ← Sample products
├── IngredientSeeder             ← Sample ingredients
├── RecipeSeeder                 ← Links products to ingredients
├── TableSeeder                  ← Restaurant tables
└── ExpenseCategorySeeder        ← Fixed expense categories
```
