<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use App\Models\Ingredient;
use Livewire\Component;

class RecipeEditor extends Component
{
    public $product_id;
    public $isOpen = false;
    
    public $recipe = []; // Array of ['ingredient_id' => x, 'amount' => y]
    
    public $availableIngredients;

    protected $listeners = ['openRecipeModal'];

    public function mount()
    {
        $this->availableIngredients = Ingredient::all();
    }

    public function openRecipeModal($productId)
    {
        $this->product_id = $productId;
        $product = Product::with('ingredients')->find($productId);
        
        $this->recipe = [];
        if ($product) {
            foreach ($product->ingredients as $ingredient) {
                $this->recipe[] = [
                    'ingredient_id' => $ingredient->id,
                    'amount' => $ingredient->pivot->amount_needed,
                ];
            }
        }
        
        $this->isOpen = true;
    }

    public function addIngredient()
    {
        $this->recipe[] = ['ingredient_id' => '', 'amount' => 0];
    }

    public function removeIngredient($index)
    {
        unset($this->recipe[$index]);
        $this->recipe = array_values($this->recipe); // Re-index array
    }

    public function save()
    {
        $product = Product::find($this->product_id);
        
        $syncData = [];
        foreach ($this->recipe as $item) {
            if (!empty($item['ingredient_id']) && $item['amount'] > 0) {
                $syncData[$item['ingredient_id']] = ['amount_needed' => $item['amount']];
            }
        }
        
        $product->ingredients()->sync($syncData);
        
        $this->isOpen = false;
        $this->dispatch('recipeSaved');
    }

    public function render()
    {
        $product = Product::find($this->product_id);
        return view('livewire.inventory.recipe-editor', [
            'productName' => $product ? $product->name : ''
        ]);
    }
}
