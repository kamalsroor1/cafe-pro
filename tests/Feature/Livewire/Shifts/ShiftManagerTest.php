<?php

namespace Tests\Feature\Livewire\Shifts;

use App\Models\Order;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ShiftManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_it_can_open_a_shift_via_ui(): void
    {
        Livewire::test('shifts.shift-manager')
            ->call('openShiftModal')
            ->set('startingCash', 150.00)
            ->call('openShift')
            ->assertSet('isOpening', false)
            ->assertSet('startingCash', 150.00);

        $this->assertDatabaseHas('shifts', [
            'started_by' => $this->user->id,
            'starting_cash' => 150.00,
            'status' => 'open',
        ]);
    }

    public function test_it_can_close_a_shift_via_ui(): void
    {
        $shift = Shift::factory()->create([
            'started_by' => $this->user->id,
            'starting_cash' => 100.00,
            'status' => 'open',
        ]);

        // Create a cash sale
        Order::factory()->create([
            'shift_id' => $shift->id,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'total' => 50.00,
        ]);

        Livewire::test('shifts.shift-manager')
            ->assertSet('activeShiftId', $shift->id)
            ->assertSet('expectedCash', 150.00) // 100 + 50
            ->call('closeShiftModal')
            ->set('endingCash', 150.00)
            ->call('closeShift')
            ->assertSet('isClosing', false)
            ->assertSet('activeShiftId', null);

        $this->assertEquals('closed', $shift->fresh()->status);
        $this->assertEquals(0, $shift->fresh()->cash_difference);
    }

    public function test_it_validates_starting_cash(): void
    {
        Livewire::test('shifts.shift-manager')
            ->set('startingCash', -10)
            ->call('openShift')
            ->assertHasErrors(['startingCash' => 'min']);
    }
}
