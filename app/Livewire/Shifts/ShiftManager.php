<?php

namespace App\Livewire\Shifts;

use App\Models\Shift;
use App\Services\ShiftService;
use Livewire\Component;

class ShiftManager extends Component
{
    public ?int $activeShiftId = null;

    public $startingCash = 0;

    public $endingCash = 0;

    public $expectedCash = 0;

    public $isOpening = false;

    public $isClosing = false;

    public function mount(ShiftService $shiftService)
    {
        $shift = $shiftService->getCurrentShift();
        $this->activeShiftId = $shift?->id;

        if ($this->activeShiftId) {
            $this->syncExpectedCash();
        }
    }

    protected function getActiveShift(): ?Shift
    {
        return $this->activeShiftId ? Shift::find($this->activeShiftId) : null;
    }

    public function syncExpectedCash(): void
    {
        $shift = $this->getActiveShift();
        if (! $shift) {
            return;
        }

        $cashSales = $shift->orders()
            ->where('payment_method', 'cash')
            ->where('payment_status', 'paid')
            ->sum('total');

        $this->expectedCash = $shift->starting_cash + $cashSales;
        $this->endingCash = $this->expectedCash;
    }

    public function openShiftModal(): void
    {
        $this->startingCash = 0;
        $this->isOpening = true;
    }

    public function openShift(ShiftService $shiftService): void
    {
        $this->validate([
            'startingCash' => 'required|numeric|min:0',
        ]);

        try {
            $shift = $shiftService->openShift(auth()->user(), $this->startingCash);
            $this->activeShiftId = $shift->id;
            $this->isOpening = false;
            $this->syncExpectedCash();
            $this->dispatch('shiftUpdated');
            session()->flash('success', 'Shift opened successfully.');
        } catch (\Exception $e) {
            $this->addError('shift', $e->getMessage());
        }
    }

    public function closeShiftModal(): void
    {
        $this->syncExpectedCash();
        $this->isClosing = true;
    }

    public function closeShift(ShiftService $shiftService): void
    {
        $this->validate([
            'endingCash' => 'required|numeric|min:0',
        ]);

        $shift = $this->getActiveShift();
        if (! $shift) {
            $this->addError('shift', 'No active shift found.');

            return;
        }

        try {
            $shiftService->closeShift($shift, $this->endingCash);
            $this->activeShiftId = null;
            $this->expectedCash = 0;
            $this->isClosing = false;
            $this->dispatch('shiftUpdated');
            session()->flash('success', 'Shift closed successfully.');
        } catch (\Exception $e) {
            $this->addError('shift', $e->getMessage());
        }
    }

    public function render()
    {
        $activeShift = $this->getActiveShift();

        return view('livewire.shifts.shift-manager', [
            'activeShift' => $activeShift,
        ])->layout('layouts.app');
    }
}
