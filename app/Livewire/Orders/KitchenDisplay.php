<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Component;

class KitchenDisplay extends Component
{
    public function getPendingOrdersProperty()
    {
        return Order::with(['items.product'])
            ->whereIn('status', ['pending', 'preparing'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function markOrderPreparing($orderId)
    {
        Order::find($orderId)?->update(['status' => 'preparing']);
    }

    public function markOrderReady($orderId)
    {
        Order::find($orderId)?->update(['status' => 'ready']);
    }

    public function markItemPreparing($itemId)
    {
        OrderItem::find($itemId)?->update(['status' => 'preparing']);
    }

    public function markItemReady($itemId)
    {
        OrderItem::find($itemId)?->update(['status' => 'ready']);
    }

    public function render()
    {
        return view('livewire.orders.kitchen-display', [
            'orders' => $this->pendingOrders,
        ])->layout('layouts.app');
    }
}
