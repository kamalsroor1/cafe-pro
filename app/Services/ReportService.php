<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shift;
use Carbon\Carbon;

class ReportService
{
    protected $profitService;

    public function __construct(ProfitService $profitService)
    {
        $this->profitService = $profitService;
    }

    public function getDailySummary(Carbon $date): array
    {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        $sales = $this->profitService->getSalesTotal($startOfDay, $endOfDay);
        $cogs = $this->profitService->calculateCOGS($startOfDay, $endOfDay);
        $expenses = $this->profitService->getExpensesTotal($startOfDay, $endOfDay);
        $wastage = $this->profitService->getWastageTotal($startOfDay, $endOfDay);

        $netProfit = $sales - $cogs - $expenses - $wastage;

        $ordersCount = Order::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('status', 'completed')
            ->count();

        return [
            'date' => $date->toDateString(),
            'sales' => $sales,
            'cogs' => $cogs,
            'expenses' => $expenses,
            'wastage' => $wastage,
            'net_profit' => $netProfit,
            'orders_count' => $ordersCount,
        ];
    }

    public function getShiftSummary(Shift $shift): array
    {
        $sales = $shift->orders()->where('status', 'completed')->sum('total');

        $cashSales = $shift->orders()
            ->where('status', 'completed')
            ->where('payment_method', 'cash')
            ->sum('total');

        $cardSales = $shift->orders()
            ->where('status', 'completed')
            ->where('payment_method', 'card')
            ->sum('total');

        $expenses = $shift->expenses()->sum('amount');

        return [
            'shift_id' => $shift->id,
            'started_at' => $shift->started_at,
            'ended_at' => $shift->ended_at,
            'starting_cash' => $shift->starting_cash,
            'expected_cash' => $shift->starting_cash + $cashSales,
            'actual_cash' => $shift->ending_cash,
            'difference' => $shift->ending_cash !== null ? ($shift->ending_cash - ($shift->starting_cash + $cashSales)) : 0,
            'total_sales' => $sales,
            'cash_sales' => $cashSales,
            'card_sales' => $cardSales,
            'expenses_logged' => $expenses,
            'orders_count' => $shift->orders()->where('status', 'completed')->count(),
        ];
    }
}
