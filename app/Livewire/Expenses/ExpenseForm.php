<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\ShiftService;
use Livewire\Component;

class ExpenseForm extends Component
{
    public $isOpen = false;

    public $categoryId = '';

    public $amount = '';

    public $description = '';

    public $expenseDate = '';

    protected $listeners = ['openExpenseModal' => 'openModal'];

    protected $rules = [
        'categoryId' => 'required|exists:expense_categories,id',
        'amount' => 'required|numeric|min:0.01',
        'description' => 'nullable|string|max:255',
        'expenseDate' => 'required|date',
    ];

    public function mount()
    {
        $this->expenseDate = now()->toDateString();
    }

    public function openModal()
    {
        $this->reset(['categoryId', 'amount', 'description']);
        $this->expenseDate = now()->toDateString();
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function save(ShiftService $shiftService)
    {
        $this->validate();

        $shift = $shiftService->getCurrentShift();

        Expense::create([
            'expense_category_id' => $this->categoryId,
            'shift_id' => $shift ? $shift->id : null,
            'recorded_by' => auth()->id(),
            'amount' => $this->amount,
            'description' => $this->description,
            'expense_date' => $this->expenseDate,
        ]);

        $this->dispatch('expenseSaved');
        $this->dispatch('toast-message', message: 'تم حفظ المصروف بنجاح', type: 'success');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.expenses.expense-form', [
            'categories' => ExpenseCategory::all(),
        ]);
    }
}
