<?php

namespace App\Livewire\Inventory;

use App\Models\Ingredient;
use Livewire\Component;
use Livewire\WithPagination;

class IngredientList extends Component
{
    use WithPagination;

    public $search = '';

    protected $listeners = ['ingredientSaved' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        Ingredient::find($id)?->delete();
    }

    public function render()
    {
        $ingredients = Ingredient::where('name', 'like', '%' . $this->search . '%')
            ->paginate(15);

        return view('livewire.inventory.ingredient-list', [
            'ingredients' => $ingredients,
        ])->layout('layouts.app');
    }
}
