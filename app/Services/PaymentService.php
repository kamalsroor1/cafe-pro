<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\Payment;

class PaymentService
{
    public function recordPayment(Order $order, array $data): Payment
    {
        $method = $data['method'] ?? PaymentMethod::Cash->value;
        $amount = $data['amount'];
        $tendered = $data['tendered'] ?? $amount;
        $change = $this->calculateChange($amount, $tendered);

        return Payment::create([
            'order_id' => $order->id,
            'method' => $method,
            'amount' => $amount,
            'tendered' => $tendered,
            'change' => $change,
            'transaction_id' => $data['transaction_id'] ?? null,
            'status' => 'completed',
        ]);
    }

    public function calculateChange(float $total, float $paid): float
    {
        return max(0, $paid - $total);
    }
}
