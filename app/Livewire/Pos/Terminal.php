<?php

namespace App\Livewire\Pos;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\RestaurantTable;
use App\Services\OrderService;
use App\Services\ShiftService;
use Livewire\Component;

class Terminal extends Component
{
    public $posMode = 'tables';

    public $selectedTable = null;

    public $activeOrderId = null;

    public $tables = [];

    public $categories;

    public $products;

    public $selectedCategory = null;

    public $cart = []; // Array of items

    public $subtotal = 0;

    public $tax = 0;

    public $total = 0;

    public $paymentMethod = 'cash';

    public $orderType = 'dine_in';

    public $activeShift = null;

    public $lastOrder = null;

    protected $listeners = ['shiftUpdated' => 'refreshShiftStatus'];

    public function refreshShiftStatus()
    {
        $this->activeShift = app(ShiftService::class)->getCurrentShift();
    }

    public function mount(ShiftService $shiftService)
    {
        $this->activeShift = $shiftService->getCurrentShift();
        $this->tables = RestaurantTable::with('activeOrder.items.product')->orderBy('name')->get();
        $this->categories = Category::whereNull('parent_id')->get();
        $this->loadProducts();
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->loadProducts();
    }

    public function openTable($tableId)
    {
        $table = RestaurantTable::with('activeOrder.items.product')->findOrFail($tableId);
        $this->selectedTable = $table;

        if ($table->status === 'occupied' && $table->activeOrder) {
            $this->activeOrderId = $table->activeOrder->id;

            $this->cart = $table->activeOrder->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                ];
            })->toArray();
        } else {
            $this->activeOrderId = null;
            $this->cart = [];
        }

        $this->calculateTotals();
        $this->posMode = 'order';
    }

    public function backToTables()
    {
        $this->posMode = 'tables';
        $this->selectedTable = null;
        $this->activeOrderId = null;
        $this->cart = [];

        $this->tables = RestaurantTable::with('activeOrder')->orderBy('name')->get();
    }

    private function syncCartToDatabase()
    {
        if (! $this->selectedTable) {
            return;
        }

        if ($this->activeOrderId) {
            $order = Order::find($this->activeOrderId);
        } else {
            $order = Order::create([
                'table_number' => $this->selectedTable->name,
                'status' => 'pending',
                'type' => 'dine_in',
                'shift_id' => $this->activeShift ? $this->activeShift->id : null,
                'cashier_id' => auth()->id(),
                'subtotal' => $this->subtotal,
                'tax' => $this->tax,
                'total' => $this->total,
                'order_number' => 'ORD-'.strtoupper(uniqid()), // Ensure order_number is not null
            ]);
            $this->activeOrderId = $order->id;
            $this->selectedTable->update(['status' => 'occupied']);
        }

        $order->items()->delete();

        foreach ($this->cart as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        $order->update([
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'total' => $this->total,
        ]);
    }

    public function loadProducts()
    {
        $query = Product::where('is_available', true);
        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }
        $this->products = $query->get();
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (! $product) {
            return;
        }

        $found = false;
        foreach ($this->cart as $index => $item) {
            if ($item['product_id'] == $product->id) {
                $this->cart[$index]['quantity']++;
                $this->cart[$index]['subtotal'] = $this->cart[$index]['quantity'] * $this->cart[$index]['unit_price'];
                $found = true;
                break;
            }
        }

        if (! $found) {
            $this->cart[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => 1,
                'unit_price' => $product->price,
                'subtotal' => $product->price,
            ];
        }

        $this->calculateTotals();
        $this->syncCartToDatabase();
    }

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        $this->calculateTotals();
        $this->syncCartToDatabase();
    }

    public function updateQuantity($index, $change)
    {
        $this->cart[$index]['quantity'] += $change;
        if ($this->cart[$index]['quantity'] <= 0) {
            $this->removeFromCart($index);
        } else {
            $this->cart[$index]['subtotal'] = $this->cart[$index]['quantity'] * $this->cart[$index]['unit_price'];
            $this->calculateTotals();
            $this->syncCartToDatabase();
        }
    }

    public function calculateTotals()
    {
        $this->subtotal = array_sum(array_column($this->cart, 'subtotal'));
        // Basic 15% tax example if needed, setting to 0 for simplicity
        $this->tax = 0;
        $this->total = $this->subtotal + $this->tax;
    }

    public function checkout(OrderService $orderService)
    {
        if (empty($this->cart)) {
            $this->addError('cart', 'Cart is empty');

            return;
        }

        if (! $this->activeShift) {
            $this->addError('shift', 'No active shift. Please open a shift first.');

            return;
        }

        try {
            $order = $orderService->createOrder([
                'type' => $this->orderType,
                'subtotal' => $this->subtotal,
                'tax' => $this->tax,
                'total' => $this->total,
                'payment_method' => $this->paymentMethod,
                'payment_status' => 'paid',
                'items' => $this->cart,
            ]);

            $this->lastOrder = $order;

            if ($this->activeOrderId) {
                $pendingOrder = Order::find($this->activeOrderId);
                if ($pendingOrder) {
                    $pendingOrder->delete();
                }
            }

            if ($this->selectedTable) {
                $this->selectedTable->update(['status' => 'available']);
            }

            $this->cart = [];
            $this->activeOrderId = null;
            $this->selectedTable = null;
            $this->posMode = 'tables';
            $this->tables = RestaurantTable::with('activeOrder')->orderBy('name')->get();
            $this->calculateTotals();

            $this->dispatch('toast-message', message: 'تم الدفع وإنهاء الطلب بنجاح', type: 'success');

        } catch (\Exception $e) {
            $this->addError('checkout', $e->getMessage());
            $this->dispatch('toast-message', message: $e->getMessage(), type: 'error');
        }
    }

    public function closeReceiptModal()
    {
        $this->lastOrder = null;
    }

    public function render()
    {
        return view('livewire.pos.terminal')->layout('layouts.pos');
    }
}
