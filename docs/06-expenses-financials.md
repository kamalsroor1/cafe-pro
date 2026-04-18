# 💰 Module 06 — Expenses & Financial Reporting

## Overview

This module tracks all business expenses and calculates **True Net Profit** using the formula:

```
Net Profit = Total Sales Revenue
           - COGS (Cost of Goods Sold)
           - Operating Expenses
```

---

## Expense Categories

| Category | Type | Examples |
|---|---|---|
| Rent | Fixed | Monthly rent |
| Salaries | Fixed | Staff wages |
| Utilities | Variable | Electricity, water, internet |
| Maintenance | Variable | Equipment repair |
| Supplies | Variable | Packaging, cups, napkins |
| Wastage | Wastage | Expired/damaged stock |
| Other | Variable | Miscellaneous |

---

## Net Profit Formula (Detailed)

```
Revenue
  = SUM(completed_orders.total_amount) for period

COGS (Cost of Goods Sold)
  = SUM(order_items.product_cost × order_items.qty)    ← from completed orders
    + SUM(wastage_logs.cost_value)                      ← wasted ingredients

Gross Profit
  = Revenue - COGS

Operating Expenses
  = SUM(expenses.amount WHERE type IN ('fixed', 'variable')) for period

Net Profit
  = Gross Profit - Operating Expenses
```

---

## Models

### `ExpenseCategory` Model
```php
class ExpenseCategory extends Model
{
    protected $fillable = ['name', 'type', 'description'];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
```

### `Expense` Model
```php
class Expense extends Model
{
    protected $fillable = [
        'expense_category_id', 'shift_id', 'recorded_by',
        'amount', 'description', 'date', 'receipt_image'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date'   => 'date',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
```

---

## API Endpoints

### Expenses

| Method | Endpoint | Permission | Description |
|---|---|---|---|
| GET | `/api/v1/expenses` | view expenses | List with filters |
| POST | `/api/v1/expenses` | manage expenses | Record expense |
| GET | `/api/v1/expenses/{id}` | view expenses | Expense detail |
| PUT | `/api/v1/expenses/{id}` | manage expenses | Update |
| DELETE | `/api/v1/expenses/{id}` | manage expenses | Delete |
| GET | `/api/v1/expense-categories` | view expenses | List categories |

### Financial Reports

| Method | Endpoint | Permission | Description |
|---|---|---|---|
| GET | `/api/v1/reports/profit` | view reports | Net profit for period |
| GET | `/api/v1/reports/sales` | view reports | Sales breakdown |
| GET | `/api/v1/reports/expenses` | view reports | Expense breakdown |
| GET | `/api/v1/reports/cogs` | view reports | COGS breakdown |
| GET | `/api/v1/reports/stock` | view reports | Current stock levels |
| GET | `/api/v1/reports/shifts` | view reports | Shift summary list |
| GET | `/api/v1/dashboard` | view reports | Dashboard summary |

Query parameters for reports: `?from=2024-01-01&to=2024-01-31`

---

## Service Spec: `ProfitService`

```php
// app/Services/ProfitService.php

class ProfitService
{
    /**
     * Calculate complete financial report for a period.
     */
    public function getReport(Carbon $from, Carbon $to): array
    {
        $revenue          = $this->calculateRevenue($from, $to);
        $cogs             = $this->calculateCOGS($from, $to);
        $grossProfit      = $revenue - $cogs;
        $operatingExpenses = $this->calculateOperatingExpenses($from, $to);
        $netProfit        = $grossProfit - $operatingExpenses;

        return [
            'period' => [
                'from' => $from->toDateString(),
                'to'   => $to->toDateString(),
            ],
            'revenue'            => round($revenue, 2),
            'cogs'               => round($cogs, 2),
            'gross_profit'       => round($grossProfit, 2),
            'gross_margin_pct'   => $revenue > 0 ? round(($grossProfit / $revenue) * 100, 2) : 0,
            'operating_expenses' => round($operatingExpenses, 2),
            'net_profit'         => round($netProfit, 2),
            'net_margin_pct'     => $revenue > 0 ? round(($netProfit / $revenue) * 100, 2) : 0,
            'breakdown' => [
                'cogs_from_recipes'  => round($this->calculateRecipeCOGS($from, $to), 2),
                'cogs_from_wastage'  => round($this->calculateWastageCOGS($from, $to), 2),
                'expenses_by_type'   => $this->getExpensesByType($from, $to),
            ],
        ];
    }

    /**
     * Total revenue from completed orders.
     */
    public function calculateRevenue(Carbon $from, Carbon $to): float
    {
        return Order::where('status', 'completed')
            ->whereBetween('completed_at', [$from->startOfDay(), $to->endOfDay()])
            ->sum('total_amount');
    }

    /**
     * Total COGS = Recipe costs + Wastage costs.
     */
    public function calculateCOGS(Carbon $from, Carbon $to): float
    {
        return $this->calculateRecipeCOGS($from, $to)
             + $this->calculateWastageCOGS($from, $to);
    }

    /**
     * COGS from recipes: sum of (product_cost × qty) for completed order items.
     */
    private function calculateRecipeCOGS(Carbon $from, Carbon $to): float
    {
        return OrderItem::whereHas('order', function ($q) use ($from, $to) {
                $q->where('status', 'completed')
                  ->whereBetween('completed_at', [$from->startOfDay(), $to->endOfDay()]);
            })
            ->selectRaw('SUM(product_cost * qty) as total')
            ->value('total') ?? 0;
    }

    /**
     * COGS from wastage logs.
     */
    private function calculateWastageCOGS(Carbon $from, Carbon $to): float
    {
        return WastageLog::whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->sum('cost_value');
    }

    /**
     * Operating expenses (fixed + variable, NOT wastage — wastage is in COGS).
     */
    public function calculateOperatingExpenses(Carbon $from, Carbon $to): float
    {
        return Expense::whereHas('category', fn($q) => $q->whereIn('type', ['fixed', 'variable']))
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->sum('amount');
    }

    /**
     * Expenses grouped by category type.
     */
    private function getExpensesByType(Carbon $from, Carbon $to): array
    {
        return Expense::with('category')
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->get()
            ->groupBy('category.type')
            ->map(fn($group) => round($group->sum('amount'), 2))
            ->toArray();
    }

    /**
     * Dashboard summary — today's key metrics.
     */
    public function getDashboardSummary(): array
    {
        $today = today();

        return [
            'today_revenue'       => $this->calculateRevenue($today, $today),
            'today_orders'        => Order::where('status', 'completed')
                                         ->whereDate('completed_at', $today)->count(),
            'today_expenses'      => Expense::whereDate('date', $today)->sum('amount'),
            'today_net_profit'    => $this->calculateRevenue($today, $today)
                                  - $this->calculateCOGS($today, $today)
                                  - $this->calculateOperatingExpenses($today, $today),
            'low_stock_count'     => Ingredient::whereColumn('stock_qty', '<=', 'min_stock_qty')->count(),
            'open_shifts_count'   => Shift::where('status', 'open')->count(),
            'pending_orders'      => Order::whereIn('status', ['pending', 'preparing'])->count(),
        ];
    }
}
```

---

## Controller Spec: `ReportController`

```php
class ReportController extends Controller
{
    public function __construct(private ProfitService $profitService) {}

    // GET /reports/profit?from=2024-01-01&to=2024-01-31
    public function profit(Request $request): JsonResponse
    {
        $request->validate([
            'from' => ['required', 'date'],
            'to'   => ['required', 'date', 'after_or_equal:from'],
        ]);

        $report = $this->profitService->getReport(
            Carbon::parse($request->from),
            Carbon::parse($request->to)
        );

        return response()->json($report);
    }

    // GET /dashboard
    public function dashboard(): JsonResponse
    {
        return response()->json(
            $this->profitService->getDashboardSummary()
        );
    }
}
```

---

## Profit Report Response Example

```json
{
  "period": { "from": "2024-01-01", "to": "2024-01-31" },
  "revenue": 45230.00,
  "cogs": 12890.50,
  "gross_profit": 32339.50,
  "gross_margin_pct": 71.49,
  "operating_expenses": 8500.00,
  "net_profit": 23839.50,
  "net_margin_pct": 52.71,
  "breakdown": {
    "cogs_from_recipes": 11200.50,
    "cogs_from_wastage": 1690.00,
    "expenses_by_type": {
      "fixed": 5000.00,
      "variable": 3500.00
    }
  }
}
```
