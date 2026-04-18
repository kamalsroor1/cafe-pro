<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class PosScreen extends Component
{
    // تخزين السلة في مصفوفة داخل الـ Component
    public $cart = [];
    public $total = 0;

    // إضافة منتج للسلة
    public function addToCart($productId)
    {
        $product = Product::find($productId);

        if (!$product) return;

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
            ];
        }

        $this->calculateTotal();
    }

    // تقليل الكمية أو الحذف
    public function removeFromCart($productId)
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] > 1) {
                $this->cart[$productId]['quantity']--;
            } else {
                unset($this->cart[$productId]);
            }
        }
        $this->calculateTotal();
    }

    // حساب الإجمالي
    public function calculateTotal()
    {
        $this->total = array_reduce($this->cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);
    }

    // مسح السلة بالكامل
    public function clearCart()
    {
        $this->cart = [];
        $this->total = 0;
    }

    public function render()
    {
        return view('livewire.pos-screen', [
            'products' => Product::all() // جلب المنتجات من SQLite
        ]);
    }
}