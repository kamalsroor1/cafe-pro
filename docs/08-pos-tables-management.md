# Table Management Feature — POS Implementation Map

## Current Problem

Currently, the shopping cart (Cart) lives only in memory inside the Livewire component (`Terminal.php`). If items are added to the cart and the user refreshes the page or navigates away, all data is lost. There is also no interface for selecting tables, suspending an order on a table, and returning to it later.

The project already has a `restaurant_tables` table with `name`, `capacity`, and `status` fields, and the `Order` model has a `table_number` column with a `pending` status. We need to wire all of this together programmatically inside the POS interface.

---

## Feature Requirements

1. **Tables Interface:** Display a grid of all restaurant tables.
2. **Status Distinction:** Green card = available table, Red card = occupied table.
3. **Table Timer:** Show how long an occupied table has been open (e.g., "Open for 15 min").
4. **Auto-Save Draft Orders:** Persist the cart to the database as a `pending` Order as soon as items are added to a table, ensuring no data loss.
5. **Resume Orders:** Ability to exit a table, return to the tables screen, open another table, then come back to the original table later to complete the order, close it, and print the receipt.

---

## Implementation Plan

### Step 1 — Database & Model Relationships

You already have `RestaurantTable` (with `status`: `available` / `occupied`) and `Order` (with `pending` status). Verify the relationship is correct in `RestaurantTable.php`:

```php
// app/Models/RestaurantTable.php

public function activeOrder(): HasOne
{
    return $this->hasOne(Order::class, 'table_number', 'name')
                ->where('status', 'pending')
                ->latest();
}
```

No new migration needed — this relies on existing columns.

---

### Step 2 — Modify `Terminal.php` (Livewire Component)

Add the following new properties to the component:

```php
// app/Livewire/Terminal.php

// Controls which screen is visible: 'tables' or 'order'
public string $posMode = 'tables';

// The currently selected table object
public ?RestaurantTable $selectedTable = null;

// The ID of the active pending order on this table (null if new)
public ?int $activeOrderId = null;
```

---

### Step 3 — New Methods in `Terminal.php`

#### `openTable(int $tableId)`
Triggered when the user clicks on a table card.

```php
public function openTable(int $tableId): void
{
    $table = RestaurantTable::with('activeOrder.items.product')->findOrFail($tableId);
    $this->selectedTable = $table;

    if ($table->status === 'occupied' && $table->activeOrder) {
        // Resume existing pending order — reload cart from DB
        $this->activeOrderId = $table->activeOrder->id;
        $this->cart = $table->activeOrder->items->map(fn($item) => [
            'product_id'   => $item->product_id,
            'name'         => $item->product->name,
            'price'        => $item->unit_price,
            'qty'          => $item->qty,
            'subtotal'     => $item->subtotal,
        ])->toArray();
    } else {
        // New table — start with empty cart
        $this->activeOrderId = null;
        $this->cart = [];
    }

    $this->posMode = 'order';
}
```

#### `addToCart(int $productId)`
Modify the existing method to also persist the cart to DB immediately:

```php
public function addToCart(int $productId): void
{
    // ... existing cart logic to add/increment item ...

    // Auto-save to DB as pending order
    $this->syncCartToDatabase();
}
```

#### `removeFromCart(int $productId)`
Same — call `syncCartToDatabase()` after modifying the cart.

#### `syncCartToDatabase()` (private)
Creates or updates the `pending` Order in the database every time the cart changes:

```php
private function syncCartToDatabase(): void
{
    if (!$this->selectedTable) return;

    $order = $this->activeOrderId
        ? Order::find($this->activeOrderId)
        : Order::create([
            'table_number' => $this->selectedTable->name,
            'status'       => 'pending',
            'shift_id'     => $this->activeShift->id,
            'user_id'      => auth()->id(),
            // ... other required fields with defaults
          ]);

    if (!$this->activeOrderId) {
        $this->activeOrderId = $order->id;

        // Mark table as occupied
        $this->selectedTable->update(['status' => 'occupied']);
    }

    // Sync order items (delete & re-insert for simplicity)
    $order->items()->delete();
    foreach ($this->cart as $item) {
        $order->items()->create([
            'product_id' => $item['product_id'],
            'qty'        => $item['qty'],
            'unit_price' => $item['price'],
            'subtotal'   => $item['subtotal'],
        ]);
    }

    // Recalculate order totals
    $order->update(['total_amount' => collect($this->cart)->sum('subtotal')]);
}
```

#### `backToTables()`
Return to the tables grid without losing data (cart is already persisted):

```php
public function backToTables(): void
{
    // Cart is already saved in DB — just reset local UI state
    $this->posMode       = 'tables';
    $this->selectedTable = null;
    $this->activeOrderId = null;
    $this->cart          = [];

    // Refresh tables list so statuses are current
    $this->tables = RestaurantTable::with('activeOrder')->get();
}
```

#### `checkout()` — Update Existing Method
After payment is confirmed, free the table:

```php
public function checkout(): void
{
    // ... existing payment logic ...

    // Mark order as completed
    $order = Order::find($this->activeOrderId);
    $order->update(['status' => 'completed']);

    // Free the table
    if ($this->selectedTable) {
        $this->selectedTable->update(['status' => 'available']);
    }

    // Reset POS state
    $this->cart          = [];
    $this->activeOrderId = null;
    $this->selectedTable = null;
    $this->posMode       = 'tables';

    // Refresh tables
    $this->tables = RestaurantTable::with('activeOrder')->get();

    // Trigger receipt print / notification
    $this->dispatch('order-completed', orderId: $order->id);
}
```

---

### Step 4 — Frontend UI (Blade / Livewire Template)

#### Tables Screen (`posMode === 'tables'`)

```blade
@if($posMode === 'tables')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-6">Select a Table</h2>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($tables as $table)
            <div
                wire:click="openTable({{ $table->id }})"
                class="p-4 rounded-xl shadow-md text-center cursor-pointer transition hover:scale-105
                {{ $table->status === 'occupied' ? 'bg-red-500 text-white' : 'bg-emerald-500 text-white' }}"
            >
                <h3 class="text-xl font-bold">{{ $table->name }}</h3>
                <p class="text-sm opacity-80">Capacity: {{ $table->capacity }}</p>

                @if($table->status === 'occupied' && $table->activeOrder)
                    {{-- Table Timer using Alpine.js --}}
                    <div
                        class="text-xs mt-2 font-medium"
                        x-data="{
                            start: new Date('{{ $table->activeOrder->created_at->toIso8601String() }}'),
                            elapsed: '',
                            tick() {
                                const diff = Math.floor((new Date() - this.start) / 60000);
                                this.elapsed = diff < 60
                                    ? diff + ' min'
                                    : Math.floor(diff / 60) + 'h ' + (diff % 60) + 'm';
                            }
                        }"
                        x-init="tick(); setInterval(() => tick(), 60000)"
                    >
                        Open for: <span x-text="elapsed" class="font-bold"></span>
                    </div>

                    <p class="text-xs mt-1 opacity-70">
                        {{ $table->activeOrder->items->count() }} item(s)
                    </p>
                @else
                    <p class="text-xs mt-2 opacity-70">Available</p>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif
```

#### Order Screen — Back Button (`posMode === 'order'`)

Add this button at the top of the existing order/cart interface:

```blade
@if($posMode === 'order')
<div class="flex items-center gap-3 p-4 border-b">
    <button
        wire:click="backToTables"
        class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition"
    >
        ← Back to Tables
    </button>

    <span class="font-semibold text-gray-800">
        Table: {{ $selectedTable?->name }}
    </span>

    @if($activeOrderId)
        <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">
            Draft Saved
        </span>
    @endif
</div>

{{-- ... rest of existing order/cart UI ... --}}
@endif
```

---

### Step 5 — Load Tables on Component Mount

Make sure the tables list is loaded when the component initializes:

```php
public function mount(): void
{
    $this->tables = RestaurantTable::with('activeOrder')->orderBy('name')->get();
    // ... rest of existing mount logic ...
}
```

---

## Table Timer — Best Approach

Use **Alpine.js** (already included with Livewire) to compute elapsed time entirely on the frontend — no server polling required:

```js
x-data="{
    start: new Date('{{ $table->activeOrder->created_at->toIso8601String() }}'),
    elapsed: '',
    tick() {
        const diff = Math.floor((new Date() - this.start) / 60000);
        this.elapsed = diff < 60
            ? diff + ' min'
            : Math.floor(diff / 60) + 'h ' + (diff % 60) + 'm';
    }
}"
x-init="tick(); setInterval(() => tick(), 60000)"
```

This runs entirely in the browser, updates every 60 seconds, and puts zero load on the server.

---

## Task Checklist

### Backend Tasks

- [ ] Add `activeOrder` relationship to `RestaurantTable` model
- [ ] Add `$posMode`, `$selectedTable`, `$activeOrderId`, `$tables` properties to `Terminal.php`
- [ ] Implement `openTable(int $tableId)` method
- [ ] Implement `syncCartToDatabase()` private method
- [ ] Update `addToCart()` to call `syncCartToDatabase()` after every change
- [ ] Update `removeFromCart()` to call `syncCartToDatabase()` after every change
- [ ] Implement `backToTables()` method
- [ ] Update `checkout()` to free the table and reset state
- [ ] Load tables in `mount()` with `activeOrder` eager-loaded

### Frontend Tasks

- [ ] Build the tables grid screen (show/hide based on `$posMode`)
- [ ] Style table cards: green (available) / red (occupied)
- [ ] Add Alpine.js timer to occupied table cards
- [ ] Add "Back to Tables" button to the order screen header
- [ ] Show "Draft Saved" badge when `$activeOrderId` is set
- [ ] Show item count on occupied table cards

### Testing Tasks

- [ ] Open a table → add items → refresh page → reopen table → verify cart is restored
- [ ] Open Table A → go back → open Table B → go back → reopen Table A → verify correct items load
- [ ] Complete checkout → verify table status returns to `available`
- [ ] Verify timer shows correct elapsed time on occupied tables
