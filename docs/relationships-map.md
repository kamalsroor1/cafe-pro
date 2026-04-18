# 🔗 Entity Relationships Map — Cafe Pro ERP

## Diagram (Text)

```
users ──────────────────────────────────────────────────────────┐
  │                                                              │
  ├── (has many) shifts                                          │
  │       │                                                      │
  │       ├── (has many) orders ─────────────────────────────┐  │
  │       │       │                                           │  │
  │       │       ├── (has many) order_items                  │  │
  │       │       │       │                                   │  │
  │       │       │       ├── (belongs to) products           │  │
  │       │       │       └── (has many) order_item_addons    │  │
  │       │       │                                           │  │
  │       │       ├── (has many) payments                     │  │
  │       │       └── (belongs to) tables                     │  │
  │       │                                                   │  │
  │       └── (has many) expenses                             │  │
  │                                                           │  │
  └── (through roles/permissions via Spatie) ─────────────────┘  │
                                                                  │
categories ──────────────────────────────────────────────────┐   │
  └── (self-referential: parent → children)                  │   │
  └── (has many) products ──────────────────────────────┐   │   │
                                                         │   │   │
products ────────────────────────────────────────────────┘   │   │
  ├── (has many) product_addons                               │   │
  └── (belongs to many) ingredients                          │   │
          via product_ingredient (amount_needed)              │   │
                                                              │   │
ingredients ─────────────────────────────────────────────────┘   │
  └── (has many) wastage_logs                                     │
                                                                  │
expense_categories                                                │
  └── (has many) expenses ──────────────────────────────────────-┘
```

---

## Eloquent Relationships

### User Model
```php
// A user has many shifts (as cashier)
public function shifts(): HasMany
{
    return $this->hasMany(Shift::class);
}

// A user has many orders (as creator)
public function orders(): HasMany
{
    return $this->hasMany(Order::class);
}

// A user has roles via Spatie (trait: HasRoles)
// use Spatie\Permission\Traits\HasRoles;
```

### Shift Model
```php
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

public function orders(): HasMany
{
    return $this->hasMany(Order::class);
}

public function expenses(): HasMany
{
    return $this->hasMany(Expense::class);
}
```

### Category Model
```php
// Self-referential
public function parent(): BelongsTo
{
    return $this->belongsTo(Category::class, 'parent_id');
}

public function children(): HasMany
{
    return $this->hasMany(Category::class, 'parent_id');
}

public function products(): HasMany
{
    return $this->hasMany(Product::class);
}
```

### Product Model
```php
public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}

public function addons(): HasMany
{
    return $this->hasMany(ProductAddon::class);
}

// Recipe relationship (BOM)
public function ingredients(): BelongsToMany
{
    return $this->belongsToMany(Ingredient::class, 'product_ingredient')
                ->withPivot('amount_needed')
                ->withTimestamps();
}
```

### Ingredient Model
```php
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
```

### Order Model
```php
public function shift(): BelongsTo
{
    return $this->belongsTo(Shift::class);
}

public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

public function table(): BelongsTo
{
    return $this->belongsTo(RestaurantTable::class, 'table_id');
}

public function items(): HasMany
{
    return $this->hasMany(OrderItem::class);
}

public function payments(): HasMany
{
    return $this->hasMany(Payment::class);
}
```

### OrderItem Model
```php
public function order(): BelongsTo
{
    return $this->belongsTo(Order::class);
}

public function product(): BelongsTo
{
    return $this->belongsTo(Product::class);
}

public function addons(): HasMany
{
    return $this->hasMany(OrderItemAddon::class);
}
```

---

## Cascade Rules Summary

| Relationship | On Delete |
|---|---|
| categories.parent_id → categories | SET NULL |
| products.category_id → categories | RESTRICT |
| product_ingredient.product_id → products | CASCADE |
| product_ingredient.ingredient_id → ingredients | CASCADE |
| order_items.order_id → orders | CASCADE |
| order_items.product_id → products | RESTRICT (financial history) |
| payments.order_id → orders | CASCADE |
| expenses.shift_id → shifts | SET NULL |
| orders.table_id → tables | SET NULL |
