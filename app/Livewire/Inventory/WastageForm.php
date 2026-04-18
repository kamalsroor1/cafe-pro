<?php

namespace App\Livewire\Inventory;

use App\Models\Ingredient;
use App\Services\StockService;
use Livewire\Component;

class WastageForm extends Component
{
    public $ingredient_id;
    public $qty_wasted;
    public $reason = 'spillage';
    public $notes;
    public $isOpen = false;

    public $availableIngredients;

    protected $listeners = ['openWastageModal'];

    protected $rules = [
        'ingredient_id' => 'required|exists:ingredients,id',
        'qty_wasted' => 'required|numeric|min:0.001',
        'reason' => 'required|in:expired,damaged,spillage,other',
        'notes' => 'nullable|string',
    ];

    public function mount()
    {
        $this->availableIngredients = Ingredient::all();
    }

    public function openWastageModal($ingredientId = null)
    {
        $this->resetValidation();
        $this->reset(['ingredient_id', 'qty_wasted', 'reason', 'notes']);
        
        if ($ingredientId) {
            $this->ingredient_id = $ingredientId;
        }
        
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function save(StockService $stockService)
    {
        $this->validate();

        $stockService->logWastage([
            'ingredient_id' => $this->ingredient_id,
            'qty_wasted' => $this->qty_wasted,
            'reason' => $this->reason,
            'notes' => $this->notes,
            // 'shift_id' will be added in phase 3
        ]);

        $this->closeModal();
        $this->dispatch('ingredientSaved'); // refresh ingredient list
    }

    public function render()
    {
        return view('livewire.inventory.wastage-form');
    }
}
