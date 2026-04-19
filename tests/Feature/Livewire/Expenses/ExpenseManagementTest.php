<?php

namespace Tests\Feature\Livewire\Expenses;

use App\Models\ExpenseCategory;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExpenseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_it_can_record_an_expense_without_shift(): void
    {
        $category = ExpenseCategory::factory()->create();

        Livewire::test('expenses.expense-form')
            ->call('openModal')
            ->set('categoryId', $category->id)
            ->set('amount', 50.00)
            ->set('description', 'Utility bill')
            ->call('save')
            ->assertDispatched('expenseSaved');

        $this->assertDatabaseHas('expenses', [
            'amount' => 50.00,
            'description' => 'Utility bill',
            'shift_id' => null,
        ]);
    }

    public function test_it_links_expense_to_active_shift_if_present(): void
    {
        $category = ExpenseCategory::factory()->create();
        $shift = Shift::factory()->create(['status' => 'open']);

        Livewire::test('expenses.expense-form')
            ->set('categoryId', $category->id)
            ->set('amount', 30.00)
            ->call('save');

        $this->assertDatabaseHas('expenses', [
            'amount' => 30.00,
            'shift_id' => $shift->id,
        ]);
    }
}
