# 🖥️ Module 04 — POS & Order Lifecycle

## Overview

The POS module covers:
- Creating orders (Dine-in, Takeaway, Delivery)
- Managing order items and add-ons
- Order status transitions
- Payment processing (Cash, Card, Split)
- Auto stock deduction on order completion

---

## Order Status Flow

```
                    ┌─────────────┐
                    │   PENDING   │  ← Order created
                    └──────┬──────┘
                           │ (kitchen confirmed)
                    ┌──────▼──────┐
                    │  PREPARING  │
                    └──────┬──────┘
                           │ (food ready)
                    ┌──────▼──────┐
                    │    READY    │
                    └──────┬──────┘
                           │ (served & paid)
                    ┌──────▼──────┐
                    │  COMPLETED  │  ← Stock deducted here
                    └─────────────┘

         From any status before COMPLETED:
                    ┌─────────────┐
                    │  CANCELLED  │
                    └─────────────┘
```

---

## Business Rules

1. **Shift Lock**: Order cannot be created without an active open shift for the user
2. **Stock Check**: (Configurable) Warn or block if ingredients are insufficient at checkout
3. **Stock Deduction**: `StockService::deductForOrder()` is called when status → `completed`
4. **Price Snapshot**: `product_name`, `product_price`, and `product_cost` are snapshotted in `order_items` at time of creation
5. **Order Number**: Auto-generated format: `ORD-YYYYMMDD-XXXX` (sequential per day)
6. **Table Release**: When order is completed or cancelled, table status → `available`

---

## Models

### `Order` Model
```php
class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number', 'shift_id', 'user_id', 'table_id', 'type', 'status',
        'subtotal', 'tax_amount', 'discount_amount', 'total_amount',
        'customer_name', 'customer_phone', 'notes',
        'completed_at', 'cancelled_at', 'cancelled_by', 'cancel_reason'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancellable(): bool
    {
        return !in_array($this->status, ['completed', 'cancelled']);
    }
}
```

---

## API Endpoints

| Method | Endpoint | Permission | Description |
|---|---|---|---|
| GET | `/api/v1/orders` | view orders | List orders (paginated, filterable) |
| POST | `/api/v1/orders` | create orders | Create new order |
| GET | `/api/v1/orders/{id}` | view orders | Full order with items & payments |
| PATCH | `/api/v1/orders/{id}/status` | update order status | Transition order status |
| POST | `/api/v1/orders/{id}/items` | create orders | Add item to order |
| DELETE | `/api/v1/orders/{id}/items/{item_id}` | create orders | Remove item (only if pending) |
| POST | `/api/v1/orders/{id}/payment` | process payments | Record payment |
| POST | `/api/v1/orders/{id}/cancel` | cancel orders | Cancel order |
| GET | `/api/v1/tables` | view orders | List tables with status |

---

## Controller Spec: `OrderController`

```php
class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private ShiftService $shiftService,
    ) {}

    // POST /orders
    public function store(StoreOrderRequest $request): JsonResponse
    {
        // 1. Validate active shift
        $shift = $this->shiftService->getActiveShiftForUser(auth()->user());
        abort_unless($shift, 403, 'No active shift. Please open a shift first.');

        // 2. Create the order
        $order = $this->orderService->createOrder(
            $request->validated(),
            $shift,
            auth()->user()
        );

        return new OrderResource($order->load(['items.addons', 'payments']));
    }

    // PATCH /orders/{order}/status
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        $this->orderService->transitionStatus($order, $request->status, auth()->user());
        return new OrderResource($order->fresh()->load('items'));
    }

    // POST /orders/{order}/cancel
    public function cancel(CancelOrderRequest $request, Order $order): JsonResponse
    {
        abort_unless($order->isCancellable(), 422, 'Order cannot be cancelled.');

        $this->orderService->cancelOrder($order, $request->reason, auth()->user());

        return response()->json(['message' => 'Order cancelled.']);
    }
}
```

---

## Service Spec: `OrderService`

```php
// app/Services/OrderService.php

class OrderService
{
    public function __construct(
        private StockService $stockService,
    ) {}

    public function createOrder(array $data, Shift $shift, User $user): Order
    {
        return DB::transaction(function () use ($data, $shift, $user) {
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'shift_id'     => $shift->id,
                'user_id'      => $user->id,
                'table_id'     => $data['table_id'] ?? null,
                'type'         => $data['type'],
                'status'       => 'pending',
                'customer_name'  => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'notes'          => $data['notes'] ?? null,
            ]);

            // Add items if provided
            if (!empty($data['items'])) {
                $this->addItemsToOrder($order, $data['items']);
            }

            // Mark table as occupied
            if ($order->table_id) {
                RestaurantTable::find($order->table_id)->update(['status' => 'occupied']);
            }

            return $order;
        });
    }

    public function addItemsToOrder(Order $order, array $items): void
    {
        foreach ($items as $itemData) {
            $product = Product::findOrFail($itemData['product_id']);

            $orderItem = $order->items()->create([
                'product_id'    => $product->id,
                'product_name'  => $product->name,        // Snapshot
                'product_price' => $product->price,       // Snapshot
                'product_cost'  => $product->cost,        // Snapshot
                'qty'           => $itemData['qty'],
                'subtotal'      => $product->price * $itemData['qty'],
                'notes'         => $itemData['notes'] ?? null,
            ]);

            // Attach add-ons
            if (!empty($itemData['addon_ids'])) {
                foreach ($itemData['addon_ids'] as $addonId) {
                    $addon = ProductAddon::findOrFail($addonId);
                    $orderItem->addons()->create([
                        'addon_id'    => $addon->id,
                        'addon_name'  => $addon->name,
                        'addon_price' => $addon->price,
                    ]);
                }
            }
        }

        $this->recalculateTotals($order);
    }

    public function transitionStatus(Order $order, string $newStatus, User $user): void
    {
        $this->validateStatusTransition($order->status, $newStatus);

        DB::transaction(function () use ($order, $newStatus, $user) {
            $updates = ['status' => $newStatus];

            if ($newStatus === 'completed') {
                $updates['completed_at'] = now();

                // Stock check (if enabled in settings)
                if (config('cafepro.stock_check_enabled')) {
                    $shortages = $this->stockService->checkStockForOrder($order);
                    if (!empty($shortages)) {
                        throw new InsufficientStockException($shortages);
                    }
                }

                // Auto-deduct stock
                $this->stockService->deductForOrder($order);

                // Free the table
                if ($order->table_id) {
                    RestaurantTable::find($order->table_id)->update(['status' => 'available']);
                }
            }

            $order->update($updates);
        });

        activity()->performedOn($order)->causedBy($user)
            ->log("Order status changed to {$newStatus}");
    }

    public function cancelOrder(Order $order, string $reason, User $user): void
    {
        $order->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => $user->id,
            'cancel_reason' => $reason,
        ]);

        if ($order->table_id) {
            RestaurantTable::find($order->table_id)->update(['status' => 'available']);
        }
    }

    private function recalculateTotals(Order $order): void
    {
        $order->load('items.addons');

        $subtotal = $order->items->sum(function ($item) {
            $addonTotal = $item->addons->sum('addon_price');
            return ($item->product_price + $addonTotal) * $item->qty;
        });

        $taxAmount = $order->items->sum(function ($item) {
            $product = Product::find($item->product_id);
            return $item->subtotal * ($product->tax_rate / 100);
        });

        $order->update([
            'subtotal'     => $subtotal,
            'tax_amount'   => $taxAmount,
            'total_amount' => $subtotal + $taxAmount - $order->discount_amount,
        ]);
    }

    private function generateOrderNumber(): string
    {
        $date  = now()->format('Ymd');
        $count = Order::whereDate('created_at', today())->count() + 1;
        return "ORD-{$date}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    private function validateStatusTransition(string $current, string $new): void
    {
        $allowed = [
            'pending'    => ['preparing', 'cancelled'],
            'preparing'  => ['ready', 'cancelled'],
            'ready'      => ['completed', 'cancelled'],
            'completed'  => [],
            'cancelled'  => [],
        ];

        if (!in_array($new, $allowed[$current] ?? [])) {
            throw new \InvalidArgumentException(
                "Cannot transition from '{$current}' to '{$new}'"
            );
        }
    }
}
```

---

## Request Specs

### `StoreOrderRequest`
```php
public function rules(): array
{
    return [
        'type'           => ['required', 'in:dine_in,takeaway,delivery'],
        'table_id'       => ['required_if:type,dine_in', 'exists:tables,id'],
        'customer_name'  => ['nullable', 'string'],
        'customer_phone' => ['nullable', 'string'],
        'notes'          => ['nullable', 'string'],
        'items'          => ['sometimes', 'array'],
        'items.*.product_id' => ['required', 'exists:products,id'],
        'items.*.qty'        => ['required', 'integer', 'min:1'],
        'items.*.addon_ids'  => ['nullable', 'array'],
        'items.*.addon_ids.*' => ['exists:product_addons,id'],
        'items.*.notes'      => ['nullable', 'string'],
    ];
}
```

### `UpdateOrderStatusRequest`
```php
public function rules(): array
{
    return [
        'status' => ['required', 'in:preparing,ready,completed,cancelled'],
    ];
}
```

### `StorePaymentRequest`
```php
public function rules(): array
{
    return [
        'method'    => ['required', 'in:cash,card,split'],
        'amount'    => ['required', 'numeric', 'min:0.01'],
        'reference' => ['nullable', 'string'],  // Card transaction ref
    ];
}
```
