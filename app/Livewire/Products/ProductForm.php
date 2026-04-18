<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;

class ProductForm extends Component
{
    public $product_id;
    public $name;
    public $category_id;
    public $price;
    public $cost;
    public $is_available = true;
    public $isOpen = false;

    protected $listeners = ['openModal'];

    protected $rules = [
        'name' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'price' => 'required|numeric|min:0',
        'cost' => 'nullable|numeric|min:0',
        'is_available' => 'boolean',
    ];

    public function openModal($data = null)
    {
        $this->resetValidation();
        
        if ($data && isset($data['product_id'])) {
            $product = Product::find($data['product_id']);
            if ($product) {
                $this->product_id = $product->id;
                $this->name = $product->name;
                $this->category_id = $product->category_id;
                $this->price = $product->price;
                $this->cost = $product->cost;
                $this->is_available = $product->is_available;
            }
        }
        
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['product_id', 'name', 'category_id', 'price', 'cost', 'is_available']);
    }

    public function save()
    {
        $this->validate();

        Product::updateOrCreate(
            ['id' => $this->product_id],
            [
                'name' => $this->name,
                'slug' => \Illuminate\Support\Str::slug($this->name),
                'category_id' => $this->category_id,
                'price' => $this->price,
                'cost' => $this->cost ?? 0,
                'is_available' => $this->is_available,
            ]
        );

        $this->closeModal();
        $this->dispatch('productSaved');
    }

    public function render()
    {
        return view('livewire.products.product-form', [
            'categories' => Category::all(),
        ]);
    }
}
