# ⏰ Module 05 — Shift Management

## Overview

The shift system controls **when** a cashier can use the POS. It creates an auditable record of every working session with opening/closing cash balances.

---

## The Cashier Cycle

```
1. OPEN SHIFT
   ├── Cashier enters opening balance (cash in drawer)
   └── System records: user_id, opening_balance, opened_at, status=open

2. WORK (all POS activity happens here)
   ├── Create orders
   ├── Process payments
   └── Log expenses/wastage

3. CLOSE SHIFT
   ├── Cashier counts actual cash in drawer
   ├── Cashier enters closing_balance
   └── System calculates:
       ├── expected_balance = opening_balance + cash_sales - cash_expenses
       └── difference = closing_balance - expected_balance
           ├── Positive → Over (cashier has more cash than expected)
           └── Negative → Short (cashier has less cash than expected)
```

---

## Business Rules

1. **One Active Shift Per User**: A user cannot open a new shift if they already have an `open` shift
2. **Shift Lock**: `OrderController` and `PaymentController` check for active shift before proceeding
3. **Expected Balance Formula**:
   ```
   expected_balance = opening_balance
                    + SUM(cash payments in this shift)
                    - SUM(cash expenses in this shift)
   ```
4. **Admin Override**: Admin can force-close a stuck open shift

---

## Model

### `Shift` Model
```php
class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'opening_balance', 'closing_balance',
        'expected_balance', 'difference', 'status', 'notes',
        'opened_at', 'closed_at'
    ];

    protected $casts = [
        'opening_balance'  => 'decimal:2',
        'closing_balance'  => 'decimal:2',
        'expected_balance' => 'decimal:2',
        'difference'       => 'decimal:2',
        'opened_at'        => 'datetime',
        'closed_at'        => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isOver(): bool
    {
        return $this->difference > 0;
    }

    public function isShort(): bool
    {
        return $this->difference < 0;
    }
}
```

---

## API Endpoints

| Method | Endpoint | Permission | Description |
|---|---|---|---|
| GET | `/api/v1/shifts` | view shifts | List my shifts (cashier) |
| GET | `/api/v1/shifts` | view all shifts | List all shifts (manager/admin) |
| POST | `/api/v1/shifts/open` | open shift | Open a new shift |
| GET | `/api/v1/shifts/active` | open shift | Get my current active shift |
| POST | `/api/v1/shifts/{id}/close` | close shift | Close shift with actual balance |
| GET | `/api/v1/shifts/{id}` | view shifts | Shift detail with summary |
| POST | `/api/v1/shifts/{id}/force-close` | manage settings | Admin force-close |

---

## Controller Spec: `ShiftController`

```php
class ShiftController extends Controller
{
    public function __construct(private ShiftService $shiftService) {}

    // POST /shifts/open
    public function open(OpenShiftRequest $request): JsonResponse
    {
        // Check no active shift already
        $existing = $this->shiftService->getActiveShiftForUser(auth()->user());
        abort_if($existing, 422, 'You already have an open shift.');

        $shift = $this->shiftService->openShift(
            auth()->user(),
            $request->opening_balance
        );

        return new ShiftResource($shift);
    }

    // GET /shifts/active
    public function active(): JsonResponse
    {
        $shift = $this->shiftService->getActiveShiftForUser(auth()->user());
        abort_unless($shift, 404, 'No active shift found.');
        return new ShiftResource($shift->load('user'));
    }

    // POST /shifts/{shift}/close
    public function close(CloseShiftRequest $request, Shift $shift): JsonResponse
    {
        $this->authorize('close', $shift); // Policy: only owner or admin

        abort_unless($shift->isOpen(), 422, 'Shift is already closed.');

        $shift = $this->shiftService->closeShift($shift, $request->closing_balance, $request->notes);

        return new ShiftResource($shift);
    }

    // GET /shifts (with role-based filtering)
    public function index(Request $request): JsonResponse
    {
        $query = Shift::with('user')->latest();

        // Cashier sees only their own shifts
        if (!auth()->user()->hasAnyRole(['admin', 'manager'])) {
            $query->where('user_id', auth()->id());
        }

        return ShiftResource::collection($query->paginate(15));
    }
}
```

---

## Service Spec: `ShiftService`

```php
// app/Services/ShiftService.php

class ShiftService
{
    public function openShift(User $user, float $openingBalance): Shift
    {
        return Shift::create([
            'user_id'         => $user->id,
            'opening_balance' => $openingBalance,
            'status'          => 'open',
            'opened_at'       => now(),
        ]);
    }

    /**
     * Get the currently open shift for a user.
     */
    public function getActiveShiftForUser(User $user): ?Shift
    {
        return Shift::where('user_id', $user->id)
                    ->where('status', 'open')
                    ->latest()
                    ->first();
    }

    /**
     * Close a shift: calculate expected vs actual balance.
     */
    public function closeShift(Shift $shift, float $closingBalance, ?string $notes): Shift
    {
        $expected = $this->calculateExpectedBalance($shift);

        $shift->update([
            'closing_balance'  => $closingBalance,
            'expected_balance' => $expected,
            'difference'       => round($closingBalance - $expected, 2),
            'status'           => 'closed',
            'closed_at'        => now(),
            'notes'            => $notes,
        ]);

        return $shift->fresh();
    }

    /**
     * Expected balance formula:
     * Opening + Cash Sales - Cash Expenses
     */
    public function calculateExpectedBalance(Shift $shift): float
    {
        $cashSales = Payment::whereHas('order', fn($q) => $q->where('shift_id', $shift->id))
            ->where('method', 'cash')
            ->sum('amount');

        $cashExpenses = Expense::where('shift_id', $shift->id)->sum('amount');

        return $shift->opening_balance + $cashSales - $cashExpenses;
    }

    /**
     * Get a summary of the shift for the close report.
     */
    public function getShiftSummary(Shift $shift): array
    {
        $orders   = $shift->orders()->with('payments')->get();
        $expenses = $shift->expenses()->sum('amount');

        return [
            'total_orders'    => $orders->count(),
            'completed_orders'=> $orders->where('status', 'completed')->count(),
            'total_sales'     => $orders->where('status', 'completed')->sum('total_amount'),
            'cash_sales'      => $shift->orders()
                ->join('payments', 'payments.order_id', '=', 'orders.id')
                ->where('payments.method', 'cash')
                ->sum('payments.amount'),
            'card_sales'      => $shift->orders()
                ->join('payments', 'payments.order_id', '=', 'orders.id')
                ->where('payments.method', 'card')
                ->sum('payments.amount'),
            'total_expenses'  => $expenses,
            'opening_balance' => $shift->opening_balance,
            'expected_balance'=> $shift->expected_balance,
            'closing_balance' => $shift->closing_balance,
            'difference'      => $shift->difference,
            'status'          => $shift->difference > 0 ? 'over' : ($shift->difference < 0 ? 'short' : 'balanced'),
        ];
    }
}
```

---

## Middleware: `EnsureShiftIsOpen`

```php
// app/Http/Middleware/EnsureShiftIsOpen.php

class EnsureShiftIsOpen
{
    public function __construct(private ShiftService $shiftService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $shift = $this->shiftService->getActiveShiftForUser(auth()->user());

        if (!$shift) {
            return response()->json([
                'message' => 'You must open a shift before performing this action.',
                'code'    => 'NO_ACTIVE_SHIFT',
            ], 403);
        }

        // Inject shift into request for downstream use
        $request->merge(['active_shift' => $shift]);

        return $next($request);
    }
}
```

Apply in routes:
```php
// routes/api.php
Route::middleware(['auth:sanctum', 'shift.open'])->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/{order}/payment', [PaymentController::class, 'store']);
});
```

Register in `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'shift.open' => \App\Http\Middleware\EnsureShiftIsOpen::class,
    ]);
})
```

---

## Close Shift Response Example

```json
{
  "id": 42,
  "user": { "id": 5, "name": "Sara Cashier" },
  "status": "closed",
  "opening_balance": 500.00,
  "expected_balance": 1250.00,
  "closing_balance": 1230.00,
  "difference": -20.00,
  "difference_status": "short",
  "opened_at": "2024-01-15T08:00:00Z",
  "closed_at": "2024-01-15T22:00:00Z",
  "summary": {
    "total_orders": 47,
    "completed_orders": 45,
    "total_sales": 1890.50,
    "cash_sales": 750.00,
    "card_sales": 1140.50,
    "total_expenses": 0.00
  }
}
```
