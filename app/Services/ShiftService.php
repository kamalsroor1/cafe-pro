<?php

namespace App\Services;

use App\Models\Shift;
use App\Models\User;

class ShiftService
{
    public function openShift(User $user, float $startingCash): Shift
    {
        // Check if there is already an open shift
        $existingShift = Shift::where('status', 'open')->first();
        if ($existingShift) {
            throw new \Exception('There is already an open shift.');
        }

        return Shift::create([
            'started_by' => $user->id,
            'starting_cash' => $startingCash,
            'started_at' => now(),
            'status' => 'open',
        ]);
    }

    public function closeShift(Shift $shift, float $endingCash): Shift
    {
        if ($shift->status !== 'open') {
            throw new \Exception('Shift is not open.');
        }

        // Calculate expected cash based on cash orders during this shift
        $cashSales = $shift->orders()
            ->where('payment_method', 'cash')
            ->where('payment_status', 'paid')
            ->sum('total');

        $expectedCash = $shift->starting_cash + $cashSales;
        $difference = $endingCash - $expectedCash;

        $shift->update([
            'closed_by' => auth()->id(),
            'ending_cash' => $endingCash,
            'expected_cash' => $expectedCash,
            'cash_difference' => $difference,
            'closed_at' => now(),
            'status' => 'closed',
        ]);

        return $shift;
    }

    public function getCurrentShift(): ?Shift
    {
        return Shift::where('status', 'open')->first();
    }
}
