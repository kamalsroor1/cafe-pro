<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Livewire\Component;
use Livewire\WithPagination;

class ExpenseList extends Component
{
    use WithPagination;

    public $categoryId = '';

    public $dateFrom = '';

    public $dateTo = '';

    protected $listeners = ['expenseSaved' => '$refresh'];

    public function delete(Expense $expense)
    {
        $expense->delete();
        session()->flash('success', 'Expense deleted successfully.');
    }

    public function render()
    {
        $query = Expense::with(['category', 'recordedBy', 'shift'])->latest();

        if ($this->categoryId) {
            $query->where('expense_category_id', $this->categoryId);
        }

        if ($this->dateFrom) {
            $query->whereDate('expense_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('expense_date', '<=', $this->dateTo);
        }

        return view('livewire.expenses.expense-list', [
            'expenses' => $query->paginate(15),
            'categories' => ExpenseCategory::all(),
        ])->layout('layouts.app');
    }
}
