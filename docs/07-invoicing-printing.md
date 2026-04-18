# 🖨️ Module 07 — Invoicing & Thermal Printing

## Overview

After an order is completed and paid, the system generates a receipt that can be:
- Displayed on screen
- Downloaded as PDF
- Sent to a thermal printer (ESC/POS protocol)

A QR code is embedded for tax authority compliance.

---

## Receipt Data Structure

```json
{
  "receipt": {
    "header": {
      "business_name": "Cafe Pro",
      "branch": "Main Branch",
      "address": "123 Coffee St, Cairo",
      "phone": "+20-10-XXXXXXXX",
      "tax_number": "TAX-123456789"
    },
    "order": {
      "order_number": "ORD-20240115-0042",
      "type": "dine_in",
      "table": "T5",
      "cashier": "Sara",
      "date": "2024-01-15",
      "time": "14:32"
    },
    "items": [
      {
        "name": "Latte",
        "qty": 2,
        "unit_price": 45.00,
        "addons": ["Extra Shot (+5.00)"],
        "line_total": 95.00
      },
      {
        "name": "Croissant",
        "qty": 1,
        "unit_price": 30.00,
        "addons": [],
        "line_total": 30.00
      }
    ],
    "totals": {
      "subtotal": 125.00,
      "tax_rate": 14,
      "tax_amount": 17.50,
      "discount": 0.00,
      "total": 142.50
    },
    "payment": {
      "method": "cash",
      "amount_paid": 150.00,
      "change": 7.50
    },
    "footer": {
      "message": "Thank you for visiting Cafe Pro!",
      "qr_data": "https://verify.tax.gov/ORD-20240115-0042"
    }
  }
}
```

---

## API Endpoints

| Method | Endpoint | Permission | Description |
|---|---|---|---|
| GET | `/api/v1/orders/{id}/receipt` | view orders | Get receipt data (JSON) |
| GET | `/api/v1/orders/{id}/receipt/pdf` | view orders | Download receipt as PDF |
| POST | `/api/v1/orders/{id}/receipt/print` | process payments | Send to thermal printer |

---

## Service Spec: `ReceiptService`

```php
// app/Services/ReceiptService.php

class ReceiptService
{
    /**
     * Build the complete receipt data array for an order.
     */
    public function buildReceiptData(Order $order): array
    {
        $order->load(['items.addons', 'payments', 'table', 'shift.user']);

        $settings = $this->getBusinessSettings(); // From config or DB settings table

        return [
            'header' => [
                'business_name' => $settings['business_name'],
                'branch'        => $settings['branch_name'],
                'address'       => $settings['address'],
                'phone'         => $settings['phone'],
                'tax_number'    => $settings['tax_number'],
            ],
            'order' => [
                'order_number' => $order->order_number,
                'type'         => $order->type,
                'table'        => $order->table?->number,
                'cashier'      => $order->shift->user->name,
                'date'         => $order->created_at->format('Y-m-d'),
                'time'         => $order->created_at->format('H:i'),
            ],
            'items' => $order->items->map(function ($item) {
                return [
                    'name'       => $item->product_name,
                    'qty'        => $item->qty,
                    'unit_price' => (float) $item->product_price,
                    'addons'     => $item->addons->map(fn($a) => "{$a->addon_name} (+{$a->addon_price})")->all(),
                    'line_total' => (float) $item->subtotal,
                ];
            })->all(),
            'totals' => [
                'subtotal'   => (float) $order->subtotal,
                'tax_amount' => (float) $order->tax_amount,
                'discount'   => (float) $order->discount_amount,
                'total'      => (float) $order->total_amount,
            ],
            'payment' => $this->buildPaymentData($order),
            'footer'  => [
                'message'  => $settings['receipt_footer'] ?? 'Thank you!',
                'qr_data'  => $this->generateQrData($order),
            ],
        ];
    }

    /**
     * Generate PDF receipt using DomPDF.
     * Template: resources/views/receipts/thermal.blade.php
     */
    public function generatePdf(Order $order): string
    {
        $data = $this->buildReceiptData($order);

        $pdf = app('dompdf.wrapper')
            ->loadView('receipts.thermal', compact('data'))
            ->setPaper([0, 0, 226.77, 600], 'portrait'); // 80mm thermal paper width

        return $pdf->output();
    }

    /**
     * QR code data for tax authority verification.
     */
    private function generateQrData(Order $order): string
    {
        // Format varies by country tax authority requirements
        return base64_encode(json_encode([
            'order'  => $order->order_number,
            'total'  => $order->total_amount,
            'tax'    => $order->tax_amount,
            'date'   => $order->created_at->toISOString(),
            'seller' => config('cafepro.tax_number'),
        ]));
    }

    private function buildPaymentData(Order $order): array
    {
        $totalPaid = $order->payments->sum('amount');
        $cashPaid  = $order->payments->where('method', 'cash')->sum('amount');

        return [
            'method'      => $order->payments->count() > 1 ? 'split' : $order->payments->first()?->method,
            'amount_paid' => (float) $totalPaid,
            'change'      => (float) max(0, $cashPaid - $order->total_amount),
            'breakdown'   => $order->payments->map(fn($p) => [
                'method'    => $p->method,
                'amount'    => (float) $p->amount,
                'reference' => $p->reference,
            ])->all(),
        ];
    }

    private function getBusinessSettings(): array
    {
        return [
            'business_name' => config('cafepro.business_name', 'Cafe Pro'),
            'branch_name'   => config('cafepro.branch_name', 'Main Branch'),
            'address'       => config('cafepro.address', ''),
            'phone'         => config('cafepro.phone', ''),
            'tax_number'    => config('cafepro.tax_number', ''),
            'receipt_footer'=> config('cafepro.receipt_footer', 'Thank you for your visit!'),
        ];
    }
}
```

---

## Controller Spec: `ReceiptController`

```php
class ReceiptController extends Controller
{
    public function __construct(private ReceiptService $receiptService) {}

    // GET /orders/{order}/receipt  — JSON data
    public function show(Order $order): JsonResponse
    {
        abort_unless($order->isCompleted(), 422, 'Receipt only available for completed orders.');
        return response()->json($this->receiptService->buildReceiptData($order));
    }

    // GET /orders/{order}/receipt/pdf
    public function pdf(Order $order): Response
    {
        abort_unless($order->isCompleted(), 422, 'Receipt only available for completed orders.');

        $pdf = $this->receiptService->generatePdf($order);

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"receipt-{$order->order_number}.pdf\"",
        ]);
    }
}
```

---

## Blade Template: `resources/views/receipts/thermal.blade.php`

```blade
<!DOCTYPE html>
<html>
<head>
<style>
  * { margin: 0; padding: 0; font-family: monospace; font-size: 11px; }
  body { width: 226px; padding: 8px; }
  .center { text-align: center; }
  .bold { font-weight: bold; }
  .divider { border-top: 1px dashed #000; margin: 6px 0; }
  .row { display: flex; justify-content: space-between; }
  .total-row { font-weight: bold; font-size: 13px; }
  img.qr { width: 80px; height: 80px; display: block; margin: 8px auto; }
</style>
</head>
<body>

<div class="center bold">{{ $data['header']['business_name'] }}</div>
<div class="center">{{ $data['header']['branch'] }}</div>
<div class="center">{{ $data['header']['address'] }}</div>
<div class="center">Tel: {{ $data['header']['phone'] }}</div>
<div class="center">Tax: {{ $data['header']['tax_number'] }}</div>

<div class="divider"></div>

<div class="row"><span>Order:</span><span>{{ $data['order']['order_number'] }}</span></div>
<div class="row"><span>Type:</span><span>{{ ucfirst($data['order']['type']) }}</span></div>
@if($data['order']['table'])
<div class="row"><span>Table:</span><span>{{ $data['order']['table'] }}</span></div>
@endif
<div class="row"><span>Cashier:</span><span>{{ $data['order']['cashier'] }}</span></div>
<div class="row"><span>Date:</span><span>{{ $data['order']['date'] }} {{ $data['order']['time'] }}</span></div>

<div class="divider"></div>

@foreach($data['items'] as $item)
<div class="row">
  <span>{{ $item['name'] }} x{{ $item['qty'] }}</span>
  <span>{{ number_format($item['line_total'], 2) }}</span>
</div>
@foreach($item['addons'] as $addon)
  <div style="padding-left:8px; color:#555">+ {{ $addon }}</div>
@endforeach
@endforeach

<div class="divider"></div>

<div class="row"><span>Subtotal</span><span>{{ number_format($data['totals']['subtotal'], 2) }}</span></div>
<div class="row"><span>Tax</span><span>{{ number_format($data['totals']['tax_amount'], 2) }}</span></div>
@if($data['totals']['discount'] > 0)
<div class="row"><span>Discount</span><span>-{{ number_format($data['totals']['discount'], 2) }}</span></div>
@endif
<div class="divider"></div>
<div class="row total-row"><span>TOTAL</span><span>{{ number_format($data['totals']['total'], 2) }}</span></div>
<div class="row"><span>Paid ({{ ucfirst($data['payment']['method']) }})</span><span>{{ number_format($data['payment']['amount_paid'], 2) }}</span></div>
@if($data['payment']['change'] > 0)
<div class="row"><span>Change</span><span>{{ number_format($data['payment']['change'], 2) }}</span></div>
@endif

<div class="divider"></div>

{!! QrCode::size(80)->generate($data['footer']['qr_data']) !!}

<div class="center" style="margin-top: 8px;">{{ $data['footer']['message'] }}</div>

</body>
</html>
```

---

## Package Installation

```bash
composer require barryvdh/laravel-dompdf
composer require simplesoftwareio/simple-qrcode

php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
php artisan vendor:publish --provider="SimpleSoftwareIO\QrCode\QrCodeServiceProvider"
```
