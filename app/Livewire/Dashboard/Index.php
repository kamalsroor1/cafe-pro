<?php

namespace App\Livewire\Dashboard;

use App\Models\Product;
use App\Services\ReportService;
use Livewire\Component;

class Index extends Component
{
    public function render(ReportService $reportService)
    {
        $todaySummary = $reportService->getDailySummary(now());

        return view('livewire.dashboard.index', [
            'totalProducts' => Product::count(),
            'todaySummary' => $todaySummary,
        ])->layout('layouts.app');
    }
}
