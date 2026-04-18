<?php

namespace App\Livewire\Pos;

use App\Models\Category;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\ShiftService;
use Livewire\Component;

class Terminal extends Component
{
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

    public function mount(ShiftService $shiftService)
    {
        $this->activeShift = $shiftService->getCurrentShift();
        $this->categories = Category::whereNull('parent_id')->get();
        $this->loadProducts();
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->loadProducts();
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
    }

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        $this->calculateTotals();
    }

    public function updateQuantity($index, $change)
    {
        $this->cart[$index]['quantity'] += $change;
        if ($this->cart[$index]['quantity'] <= 0) {
            $this->removeFromCart($index);
        } else {
            $this->cart[$index]['subtotal'] = $this->cart[$index]['quantity'] * $this->cart[$index]['unit_price'];
            $this->calculateTotals();
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
            $orderService->createOrder([
                'type' => $this->orderType,
                'subtotal' => $this->subtotal,
                'tax' => $this->tax,
                'total' => $this->total,
                'payment_method' => $this->paymentMethod,
                'payment_status' => 'paid',
                'items' => $this->cart,
            ]);

            $this->cart = [];
            $this->calculateTotals();
            session()->flash('success', 'Order completed successfully!');

        } catch (\Exception $e) {
            $this->addError('checkout', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pos.terminal')->layout('layouts.pos');
    }
}
