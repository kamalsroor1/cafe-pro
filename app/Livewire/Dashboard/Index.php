<?php

namespace App\Livewire\Dashboard;

use App\Models\Product;
use App\Models\Order;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.dashboard.index', [
            'totalProducts' => Product::count(),
        ])->layout('layouts.app');
    }
}
