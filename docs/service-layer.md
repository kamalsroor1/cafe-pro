# ⚙️ Service Layer — Cafe Pro ERP

## Principle

> **All business logic lives in Services, never in Controllers.**

Controllers are thin: they receive HTTP requests, delegate to a Service, and return a response.  
Services are fat: they contain all domain logic, DB transactions, and inter-service calls.

---

## Service Inventory

| Service | File | Responsibility |
|---|---|---|
| `AuthService` | `app/Services/AuthService.php` | Login, token management |
| `ShiftService` | `app/Services/ShiftService.php` | Open/close shifts, balance calc |
| `OrderService` | `app/Services/OrderService.php` | Order creation, status transitions |
| `StockService` | `app/Services/StockService.php` | Stock deduction, wastage, checks |
| `RecipeService` | `app/Services/RecipeService.php` | Recipe cost calculation |
| `PaymentService` | `app/Services/PaymentService.php` | Process payments, split payment |
| `ProfitService` | `app/Services/ProfitService.php` | Revenue, COGS, net profit |
| `ReceiptService` | `app/Services/ReceiptService.php` | Build receipt data, generate PDF |
| `ReportService` | `app/Services/ReportService.php` | Sales, stock, shift reports |

---

## Dependency Map

```
OrderController
    └── OrderService
            ├── ShiftService      (validate active shift)
            ├── StockService      (check & deduct stock on complete)
            └── PaymentService    (process payment)

ReportController
    └── ProfitService
            (queries Orders, OrderItems, Expenses, WastageLogs directly)

ReceiptController
    └── ReceiptService
            (uses Order data + QR generation)
```

---

## Service Registration

All services are registered via **constructor injection** — Laravel's IoC container resolves them automatically. No manual binding needed unless using interfaces.

```php
// Controller — automatic injection
class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly ShiftService $shiftService,
    ) {}
}
```

If using interfaces (recommended for large projects):

```php
// app/Providers/AppServiceProvider.php
public function register(): void
{
    $this->app->bind(
        \App\Contracts\StockServiceInterface::class,
        \App\Services\StockService::class,
    );
}
```

---

## Service Template

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExampleService
{
    /**
     * All public methods represent use cases.
     * Keep private methods as internal helpers only.
     */
    public function doSomething(array $data): mixed
    {
        return DB::transaction(function () use ($data) {
            // 1. Validate business rules
            // 2. Perform DB operations
            // 3. Trigger side effects (events, notifications)
            // 4. Return result
        });
    }
}
```

---

## Error Handling in Services

Services should throw domain exceptions, not return HTTP responses:

```php
// app/Exceptions/InsufficientStockException.php
class InsufficientStockException extends \RuntimeException
{
    public function __construct(public readonly array $shortages)
    {
        parent::__construct('Insufficient stock for one or more ingredients.');
    }
}

// app/Exceptions/NoActiveShiftException.php
class NoActiveShiftException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('No active shift found for this user.');
    }
}
```

Map exceptions to HTTP responses in `bootstrap/app.php`:

```php
->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (InsufficientStockException $e, Request $request) {
        return response()->json([
            'message'   => $e->getMessage(),
            'shortages' => $e->shortages,
        ], 422);
    });

    $exceptions->render(function (NoActiveShiftException $e, Request $request) {
        return response()->json([
            'message' => $e->getMessage(),
            'code'    => 'NO_ACTIVE_SHIFT',
        ], 403);
    });
})
```

---

## PaymentService (Full Spec)

```php
// app/Services/PaymentService.php

class PaymentService
{
    /**
     * Process a payment for an order.
     * Handles cash, card, and split payments.
     */
    public function processPayment(Order $order, array $paymentData): Payment
    {
        // Validate payment amount covers order total
        $totalPaid = $order->payments()->sum('amount') + $paymentData['amount'];

        // Store the payment
        $payment = $order->payments()->create([
            'method'    => $paymentData['method'],
            'amount'    => $paymentData['amount'],
            'reference' => $paymentData['reference'] ?? null,
        ]);

        // If fully paid, auto-complete the order (optional flow)
        if ($totalPaid >= $order->total_amount && $order->status === 'ready') {
            app(OrderService::class)->transitionStatus($order, 'completed', auth()->user());
        }

        return $payment;
    }

    /**
     * Calculate change for cash payment.
     */
    public function calculateChange(Order $order, float $cashGiven): float
    {
        return max(0, $cashGiven - $order->total_amount);
    }
}
```

---

## Coding Standards for Services

1. **One public method = one use case** — don't create god services
2. **Use DB::transaction()** for any multi-step write operations
3. **Log important actions** using `activity()->log()` (Spatie)
4. **Never return HTTP responses** from services — that's the controller's job
5. **Accept models or IDs** — prefer accepting already-resolved models
6. **Don't query inside loops** — use eager loading before passing data in
