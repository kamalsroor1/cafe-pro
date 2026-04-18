<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
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

    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);

        return $order;
    }
}
