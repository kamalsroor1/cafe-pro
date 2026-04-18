<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    protected $stockService;

    protected $shiftService;

    public function __construct(StockService $stockService, ShiftService $shiftService)
    {
        $this->stockService = $stockService;
        $this->shiftService = $shiftService;
    }

    public function createOrder(array $data): Order
    {
        $currentShift = $this->shiftService->getCurrentShift();
        if (! $currentShift) {
            throw new \Exception('No active shift. Please open a shift first.');
        }

        // Check stock if enabled
        if (config('cafepro.stock_check_enabled', true)) {
            $productQuantities = [];
            foreach ($data['items'] as $item) {
                if (! isset($productQuantities[$item['product_id']])) {
                    $productQuantities[$item['product_id']] = 0;
                }
                $productQuantities[$item['product_id']] += $item['quantity'];
            }

            $shortages = $this->stockService->checkStockForProducts($productQuantities);
            if (! empty($shortages)) {
                // Formatting shortages for exception message could be done here or handled by caller
                throw new \Exception('Insufficient stock for some products.');
            }
        }

        return DB::transaction(function () use ($data, $currentShift) {
            // Create Order
            $order = Order::create([
                'order_number' => 'ORD-'.strtoupper(Str::random(8)),
                'shift_id' => $currentShift->id,
                'cashier_id' => auth()->id(),
                'type' => $data['type'] ?? 'dine_in',
                'table_number' => $data['table_number'] ?? null,
                'status' => 'pending',
                'subtotal' => $data['subtotal'],
                'tax' => $data['tax'] ?? 0,
                'discount' => $data['discount'] ?? 0,
                'total' => $data['total'],
                'payment_method' => $data['payment_method'] ?? null,
                'payment_status' => $data['payment_status'] ?? 'unpaid',
            ]);

            $productQuantities = [];

            // Create Order Items
            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                    'addons' => $item['addons'] ?? null,
                ]);

                if (! isset($productQuantities[$item['product_id']])) {
                    $productQuantities[$item['product_id']] = 0;
                }
                $productQuantities[$item['product_id']] += $item['quantity'];
            }

            // Deduct Stock
            $this->stockService->deductForProducts($productQuantities);

            return $order;
        });
    }

    public function addItemsToOrder(Order $order, array $items): void
    {
        // Implementation for adding items later
    }

    public function transitionStatus(Order $order, string $newStatus, User $user): void
    {
        $this->validateStatusTransition($order->status->value, $newStatus);
        $order->update(['status' => $newStatus]);
    }

    public function cancelOrder(Order $order, string $reason, User $user): void
    {
        $order->update([
            'status' => OrderStatus::Cancelled->value,
            'notes' => $order->notes."\nCancelled reason: ".$reason,
        ]);
        // Also maybe restore stock
    }

    public function recalculateTotals(Order $order): void
    {
        $subtotal = $order->items()->sum('subtotal');
        // add taxes/discounts logic
        $order->update([
            'subtotal' => $subtotal,
            'total' => $subtotal + $order->tax - $order->discount,
        ]);
    }

    public function generateOrderNumber(): string
    {
        return 'ORD-'.strtoupper(Str::random(8));
    }

    public function validateStatusTransition(string $current, string $new): void
    {
        // Example logic
        $validTransitions = [
            'pending' => ['preparing', 'cancelled'],
            'preparing' => ['ready', 'cancelled'],
            'ready' => ['completed'],
            'completed' => [],
            'cancelled' => [],
        ];

        if (! in_array($new, $validTransitions[$current] ?? [])) {
            throw new \Exception("Invalid status transition from {$current} to {$new}");
        }
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);

        return $order;
    }
}
