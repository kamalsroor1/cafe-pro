<?php

namespace App\Livewire\Reports;

use App\Models\Shift;
use App\Services\ReportService;
use Livewire\Component;
use Livewire\WithPagination;

class ShiftReport extends Component
{
    use WithPagination;

    public $dateFrom;

    public $dateTo;

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo = now()->toDateString();
    }

    public function export(ReportService $reportService)
    {
        $query = Shift::with('user')->latest();

        if ($this->dateFrom) {
            $query->whereDate('started_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('started_at', '<=', $this->dateTo);
        }

        $shifts = $query->get();
        $summaries = [];

        foreach ($shifts as $shift) {
            $summaries[$shift->id] = $reportService->getShiftSummary($shift);
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ShiftReportExport($shifts, $summaries),
            'shift-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function render(ReportService $reportService)
    {
        $query = Shift::with('user')->latest();

        if ($this->dateFrom) {
            $query->whereDate('started_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('started_at', '<=', $this->dateTo);
        }

        $shifts = $query->paginate(15);
        $summaries = [];

        foreach ($shifts as $shift) {
            $summaries[$shift->id] = $reportService->getShiftSummary($shift);
        }

        return view('livewire.reports.shift-report', [
            'shifts' => $shifts,
            'summaries' => $summaries,
        ])->layout('layouts.app');
    }
}
