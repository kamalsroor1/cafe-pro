<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Order;
use App\Models\WastageLog;
use Carbon\Carbon;

class ProfitService
{
    public function calculateNetProfit(Carbon $from, Carbon $to): float
    {
        $sales = $this->getSalesTotal($from, $to);
        $cogs = $this->calculateCOGS($from, $to);
        $expenses = $this->getExpensesTotal($from, $to);
        $wastage = $this->getWastageTotal($from, $to);

        return $sales - $cogs - $expenses - $wastage;
    }

    public function calculateCOGS(Carbon $from, Carbon $to): float
    {
        // Cost of Goods Sold
        // This is a simplified calculation: sum of (product cost * quantity) for all completed orders
        $orders = Order::whereBetween('created_at', [$from, $to])
            ->where('status', 'completed')
            ->with('items.product')
            ->get();

        $cogs = 0;
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                // Assuming product has a cost property. If not, this logic might need adjustment.
                // We'll use a placeholder or check if cost exists.
                $cost = $item->product->cost ?? ($item->product->price * 0.3); // fallback to 30% of price if cost missing
                $cogs += $cost * $item->quantity;
            }
        }

        return $cogs;
    }

    public function getSalesTotal(Carbon $from, Carbon $to): float
    {
        return Order::whereBetween('created_at', [$from, $to])
            ->where('status', 'completed')
            ->sum('total');
    }

    public function getExpensesTotal(Carbon $from, Carbon $to): float
    {
        return Expense::whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])
            ->sum('amount');
    }

    public function getWastageTotal(Carbon $from, Carbon $to): float
    {
        // Calculate cost of wasted items
        $wastages = WastageLog::whereBetween('created_at', [$from, $to])
            ->with('ingredient')
            ->get();

        $totalWastageCost = 0;
        foreach ($wastages as $wastage) {
            $costPerUnit = $wastage->ingredient->cost_per_unit ?? 0;
            $totalWastageCost += ($costPerUnit * $wastage->quantity);
        }

        return $totalWastageCost;
    }
}
