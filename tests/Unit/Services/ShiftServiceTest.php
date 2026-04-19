<?php

namespace Tests\Unit\Services;

use App\Models\Order;
use App\Models\Shift;
use App\Models\User;
use App\Services\ShiftService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiftServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ShiftService $shiftService;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shiftService = new ShiftService();
        $this->user = User::factory()->create();
    }

    public function test_it_can_open_a_shift(): void
    {
        $shift = $this->shiftService->openShift($this->user, 100.00);

        $this->assertDatabaseHas('shifts', [
            'id' => $shift->id,
            'started_by' => $this->user->id,
            'starting_cash' => 100.00,
            'status' => 'open',
        ]);
    }

    public function test_it_cannot_open_multiple_shifts_simultaneously(): void
    {
        $this->shiftService->openShift($this->user, 100.00);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('There is already an open shift.');

        $this->shiftService->openShift($this->user, 200.00);
    }

    public function test_it_calculates_expected_balance_correctly(): void
    {
        $shift = $this->shiftService->openShift($this->user, 100.00);

        // Create some cash orders
        Order::factory()->count(2)->create([
            'shift_id' => $shift->id,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'total' => 50.00,
        ]);

        // Create a non-cash order (should be ignored in cash balance)
        Order::factory()->create([
            'shift_id' => $shift->id,
            'payment_method' => 'card',
            'payment_status' => 'paid',
            'total' => 50.00,
        ]);

        $expectedBalance = $this->shiftService->calculateExpectedBalance($shift);

        // 100 starting + 100 cash sales = 200
        $this->assertEquals(200.00, $expectedBalance);
    }

    public function test_it_can_close_a_shift(): void
    {
        $shift = $this->shiftService->openShift($this->user, 100.00);
        
        $this->actingAs($this->user); // For auth()->id() in service

        $closedShift = $this->shiftService->closeShift($shift, 150.00);

        $this->assertEquals('closed', $closedShift->status);
        $this->assertEquals(150.00, $closedShift->ending_cash);
        $this->assertEquals(100.00, $closedShift->expected_cash);
        $this->assertEquals(50.00, $closedShift->cash_difference);
        $this->assertNotNull($closedShift->closed_at);
    }
}
