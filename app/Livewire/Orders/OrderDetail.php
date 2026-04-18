<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Livewire\Component;

class OrderDetail extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        $this->order = $order->load(['items.product', 'payments', 'shift']);
    }

    public function render()
    {
        return view('livewire.orders.order-detail')->layout('layouts.app');
    }
}
