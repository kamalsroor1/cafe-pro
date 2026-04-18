# 🍽️ Module 02 — Categories & Products

## Overview

Menu is structured as a 2-level hierarchy:  
**Category** → **Sub-category** → **Product**

Products have: price, auto-calculated cost (from recipe), tax rate, and add-ons.

---

## Models

### `Category` Model
```php
// app/Models/Category.php
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'parent_id', 'description', 'image', 'sort_order', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

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
}
```

### `Product` Model
```php
// app/Models/Product.php
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'image',
        'price', 'cost', 'tax_rate', 'is_available', 'sort_order'
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'cost'         => 'decimal:2',
        'tax_rate'     => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(ProductAddon::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'product_ingredient')
                    ->withPivot('amount_needed')
                    ->withTimestamps();
    }

    // Computed: price including tax
    public function getPriceWithTaxAttribute(): float
    {
        return $this->price * (1 + $this->tax_rate / 100);
    }
}
```

---

## API Endpoints

### Categories

| Method | Endpoint | Permission | Description |
|---|---|---|---|
| GET | `/api/v1/categories` | view categories | List all (with children) |
| POST | `/api/v1/categories` | manage categories | Create category |
| GET | `/api/v1/categories/{id}` | view categories | Get with children & products |
| PUT | `/api/v1/categories/{id}` | manage categories | Update |
| DELETE | `/api/v1/categories/{id}` | manage categories | Delete (only if no products) |

### Products

| Method | Endpoint | Permission | Description |
|---|---|---|---|
| GET | `/api/v1/products` | view products | List (filter by category) |
| POST | `/api/v1/products` | manage products | Create |
| GET | `/api/v1/products/{id}` | view products | Get with addons & recipe |
| PUT | `/api/v1/products/{id}` | manage products | Update |
| DELETE | `/api/v1/products/{id}` | manage products | Soft delete |

---

## Controller Spec: `CategoryController`

```php
class CategoryController extends Controller
{
    // GET /categories
    // Returns nested tree: top-level categories with children
    public function index(): JsonResponse
    {
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return CategoryResource::collection($categories);
    }

    // POST /categories
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        // Auto-generate slug from name
        $category = Category::create([
            ...$request->validated(),
            'slug' => Str::slug($request->name),
        ]);

        return new CategoryResource($category);
    }

    // GET /categories/{id}
    public function show(Category $category): JsonResponse
    {
        return new CategoryResource(
            $category->load(['children', 'products'])
        );
    }

    // PUT /categories/{id}
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());
        return new CategoryResource($category);
    }

    // DELETE /categories/{id}
    public function destroy(Category $category): JsonResponse
    {
        // Guard: cannot delete if has products
        if ($category->products()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category with existing products.'
            ], 422);
        }
        $category->delete();
        return response()->json(['message' => 'Category deleted.']);
    }
}
```

---

## Controller Spec: `ProductController`

```php
class ProductController extends Controller
{
    // GET /products?category_id=&available=1
    public function index(Request $request): JsonResponse
    {
        $products = Product::with(['category', 'addons'])
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->available, fn($q) => $q->where('is_available', true))
            ->orderBy('sort_order')
            ->paginate(20);

        return ProductResource::collection($products);
    }

    // POST /products
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create([
            ...$request->validated(),
            'slug' => Str::slug($request->name),
        ]);

        return new ProductResource($product->load('category'));
    }

    // GET /products/{id}
    public function show(Product $product): JsonResponse
    {
        return new ProductResource(
            $product->load(['category', 'addons', 'ingredients'])
        );
    }

    // PUT /products/{id}
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());
        return new ProductResource($product);
    }

    // DELETE /products/{id} — soft delete
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json(['message' => 'Product archived.']);
    }
}
```

---

## Request Specs

### `StoreProductRequest`
```php
public function rules(): array
{
    return [
        'category_id'  => ['required', 'exists:categories,id'],
        'name'         => ['required', 'string', 'max:255'],
        'description'  => ['nullable', 'string'],
        'price'        => ['required', 'numeric', 'min:0'],
        'tax_rate'     => ['nullable', 'numeric', 'min:0', 'max:100'],
        'is_available' => ['boolean'],
        'sort_order'   => ['integer'],
    ];
}
```

---

## API Resource: `ProductResource`

```php
public function toArray(Request $request): array
{
    return [
        'id'             => $this->id,
        'name'           => $this->name,
        'slug'           => $this->slug,
        'description'    => $this->description,
        'image'          => $this->image,
        'price'          => (float) $this->price,
        'price_with_tax' => (float) $this->price_with_tax,
        'cost'           => (float) $this->cost,
        'tax_rate'       => (float) $this->tax_rate,
        'is_available'   => $this->is_available,
        'category'       => new CategoryResource($this->whenLoaded('category')),
        'addons'         => ProductAddonResource::collection($this->whenLoaded('addons')),
        'ingredients'    => IngredientResource::collection($this->whenLoaded('ingredients')),
    ];
}
```
