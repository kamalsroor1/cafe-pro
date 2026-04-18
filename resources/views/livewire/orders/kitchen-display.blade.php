<div wire:poll.10s>
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-100">Kitchen Display System (KDS)</h2>
        <span class="px-4 py-2 bg-surface border border-[#2A2A2A] rounded-lg text-gray-400 text-sm">
            Live Updates (every 10s)
        </span>
    </div>

    @if($orders->isEmpty())
        <div class="h-64 flex items-center justify-center text-gray-500 bg-surface border border-[#2A2A2A] rounded-2xl">
            No active orders. Kitchen is all caught up!
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($orders as $order)
                <div class="bg-surface border {{ $order->status === 'preparing' ? 'border-amber-500 shadow-lg shadow-amber-500/10' : 'border-[#2A2A2A]' }} rounded-2xl flex flex-col overflow-hidden transition-all">
                    {{-- Order Header --}}
                    <div class="p-4 bg-elevated border-b border-[#2A2A2A] flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-gray-100 text-lg">#{{ $order->order_number }}</h3>
                            <div class="flex gap-2 mt-1">
                                <span class="text-xs text-gray-400">{{ $order->type === 'dine_in' ? 'Dine In' : ($order->type === 'takeaway' ? 'Takeaway' : 'Delivery') }}</span>
                                @if($order->table_number)
                                    <span class="text-xs text-amber-500 font-bold">Table {{ $order->table_number }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right text-sm">
                            <div class="text-gray-400">{{ $order->created_at->diffForHumans() }}</div>
                        </div>
                    </div>

                    {{-- Order Items --}}
                    <div class="p-4 flex-1 overflow-y-auto space-y-3 bg-base">
                        @foreach($order->items as $item)
                            <div class="flex items-center justify-between p-3 rounded-xl border border-[#2A2A2A] {{ $item->status === 'ready' ? 'opacity-50' : 'bg-surface' }}">
                                <div class="flex gap-3">
                                    <span class="font-bold text-amber-500 text-lg">{{ $item->quantity }}x</span>
                                    <div>
                                        <div class="font-medium text-gray-100 {{ $item->status === 'ready' ? 'line-through text-gray-500' : '' }}">{{ $item->product->name }}</div>
                                        @if($item->notes)
                                            <div class="text-xs text-red-400 mt-1">Note: {{ $item->notes }}</div>
                                        @endif
                                    </div>
                                </div>
                                @if($item->status !== 'ready')
                                    <button wire:click="markItemReady({{ $item->id }})" class="p-2 rounded-lg bg-elevated text-emerald-500 hover:bg-emerald-500 hover:text-white transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Order Actions --}}
                    <div class="p-4 border-t border-[#2A2A2A] grid grid-cols-2 gap-3 shrink-0">
                        @if($order->status === 'pending')
                            <button wire:click="markOrderPreparing({{ $order->id }})" class="col-span-2 py-3 rounded-xl bg-amber-500 text-black font-bold hover:bg-amber-400 transition-colors">
                                Start Preparing
                            </button>
                        @elseif($order->status === 'preparing')
                            <button wire:click="markOrderReady({{ $order->id }})" class="col-span-2 py-3 rounded-xl bg-emerald-500 text-white font-bold hover:bg-emerald-400 transition-colors">
                                Mark Order Ready
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
