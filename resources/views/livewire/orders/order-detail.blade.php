<div>
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('orders.index') }}" class="w-10 h-10 bg-surface border border-[#2A2A2A] rounded-xl flex items-center justify-center text-gray-400 hover:text-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-100">Order {{ $order->order_number }}</h2>
        <span class="px-3 py-1 rounded-full text-xs font-bold 
            {{ $order->status === 'completed' ? 'bg-emerald-500/20 text-emerald-400' : '' }}
            {{ $order->status === 'cancelled' ? 'bg-red-500/20 text-red-400' : '' }}
            {{ in_array($order->status, ['pending', 'preparing', 'ready']) ? 'bg-amber-500/20 text-amber-500' : '' }}
        ">
            {{ ucfirst($order->status->value ?? $order->status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-100 mb-4">Items</h3>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex justify-between items-center py-2 border-b border-[#2A2A2A] last:border-0">
                            <div>
                                <p class="text-gray-100 font-medium">{{ $item->quantity }}x {{ $item->product->name }}</p>
                                @if($item->addons)
                                    <p class="text-xs text-gray-400 mt-1">Addons included</p>
                                @endif
                            </div>
                            <span class="text-gray-100 font-bold">${{ number_format($item->subtotal, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-100 mb-4">Payments</h3>
                <div class="space-y-4">
                    @forelse($order->payments as $payment)
                        <div class="flex justify-between items-center py-2 border-b border-[#2A2A2A] last:border-0">
                            <div>
                                <p class="text-gray-100 font-medium capitalize">{{ $payment->method->value ?? $payment->method }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <span class="text-emerald-400 font-bold">${{ number_format($payment->amount, 2) }}</span>
                        </div>
                    @empty
                        <p class="text-gray-400 italic">No payments recorded.</p>
                    @endforelse
                </div>
            </div>
        </div>
        
        <div class="space-y-6">
            <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-100 mb-4">Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-gray-400">
                        <span>Subtotal</span>
                        <span>${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-400">
                        <span>Tax</span>
                        <span>${{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-400">
                        <span>Discount</span>
                        <span>-${{ number_format($order->discount, 2) }}</span>
                    </div>
                    <div class="pt-3 mt-3 border-t border-[#2A2A2A] flex justify-between items-center text-gray-100 font-bold text-lg">
                        <span>Total</span>
                        <span class="text-amber-500">${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-100 mb-4">Details</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Date</span>
                        <span class="text-gray-100">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Type</span>
                        <span class="text-gray-100 capitalize">{{ str_replace('_', ' ', $order->type->value ?? $order->type) }}</span>
                    </div>
                    @if($order->table_number)
                    <div class="flex justify-between">
                        <span class="text-gray-400">Table</span>
                        <span class="text-gray-100">{{ $order->table_number }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-400">Shift ID</span>
                        <span class="text-gray-100">#{{ $order->shift_id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
