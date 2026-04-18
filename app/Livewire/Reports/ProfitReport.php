<?php

namespace App\Livewire\Reports;

use App\Services\ReportService;
use Carbon\Carbon;
use Livewire\Component;

class ProfitReport extends Component
{
    public $dateFrom;

    public $dateTo;

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo = now()->toDateString();
    }

    public function render(ReportService $reportService)
    {
        $from = Carbon::parse($this->dateFrom)->startOfDay();
        $to = Carbon::parse($this->dateTo)->endOfDay();

        // Let's get daily summaries for the range
        $days = [];
        $current = $from->copy();

        $totalSales = 0;
        $totalCOGS = 0;
        $totalExpenses = 0;
        $totalWastage = 0;

        while ($current <= $to) {
            $summary = $reportService->getDailySummary($current);
            $days[] = $summary;

            $totalSales += $summary['sales'];
            $totalCOGS += $summary['cogs'];
            $totalExpenses += $summary['expenses'];
            $totalWastage += $summary['wastage'];

            $current->addDay();
        }

        $netProfit = $totalSales - $totalCOGS - $totalExpenses - $totalWastage;

        return view('livewire.reports.profit-report', [
            'days' => array_reverse($days), // Show newest first
            'totals' => [
                'sales' => $totalSales,
                'cogs' => $totalCOGS,
                'expenses' => $totalExpenses,
                'wastage' => $totalWastage,
                'net_profit' => $netProfit,
            ],
        ])->layout('layouts.app');
    }
}
