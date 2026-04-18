<?php

namespace App\Livewire\Inventory;

use App\Models\Ingredient;
use Livewire\Component;

class IngredientForm extends Component
{
    public $ingredient_id;
    public $name;
    public $unit = 'kg';
    public $stock_qty = 0;
    public $min_stock_qty = 0;
    public $cost_per_unit = 0;
    public $supplier;
    public $isOpen = false;

    protected $listeners = ['openModal'];

    protected $rules = [
        'name' => 'required|string|max:255',
        'unit' => 'required|in:g,kg,ml,l,pcs,tbsp,tsp',
        'stock_qty' => 'required|numeric|min:0',
        'min_stock_qty' => 'required|numeric|min:0',
        'cost_per_unit' => 'required|numeric|min:0',
        'supplier' => 'nullable|string|max:255',
    ];

    public function openModal($data = null)
    {
        $this->resetValidation();
        
        if ($data && isset($data['id'])) {
            $ingredient = Ingredient::find($data['id']);
            if ($ingredient) {
                $this->ingredient_id = $ingredient->id;
                $this->name = $ingredient->name;
                $this->unit = $ingredient->unit;
                $this->stock_qty = $ingredient->stock_qty;
                $this->min_stock_qty = $ingredient->min_stock_qty;
                $this->cost_per_unit = $ingredient->cost_per_unit;
                $this->supplier = $ingredient->supplier;
            }
        }
        
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['ingredient_id', 'name', 'unit', 'stock_qty', 'min_stock_qty', 'cost_per_unit', 'supplier']);
    }

    public function save()
    {
        $this->validate();

        Ingredient::updateOrCreate(
            ['id' => $this->ingredient_id],
            [
                'name' => $this->name,
                'unit' => $this->unit,
                'stock_qty' => $this->stock_qty,
                'min_stock_qty' => $this->min_stock_qty,
                'cost_per_unit' => $this->cost_per_unit,
                'supplier' => $this->supplier,
            ]
        );

        $this->closeModal();
        $this->dispatch('ingredientSaved');
        $this->dispatch('toast-message', message: 'تم حفظ المكون بنجاح', type: 'success');
    }

    public function render()
    {
        return view('livewire.inventory.ingredient-form');
    }
}
