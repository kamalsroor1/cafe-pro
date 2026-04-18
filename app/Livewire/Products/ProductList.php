<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    public $search = '';

    protected $listeners = ['productSaved' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        Product::find($id)?->delete();
    }

    public function render()
    {
        $products = Product::with('category')
            ->where('name', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.products.product-list', [
            'products' => $products,
        ])->layout('layouts.app');
    }
}
