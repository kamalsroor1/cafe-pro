<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class OrderList extends Component
{
    use WithPagination;

    #[Url]
    public $status = '';

    #[Url]
    public $date = '';

    public function render()
    {
        $query = Order::with(['shift', 'table'])->latest();

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->date) {
            $query->whereDate('created_at', $this->date);
        }

        return view('livewire.orders.order-list', [
            'orders' => $query->paginate(15),
        ])->layout('layouts.app');
    }
}
