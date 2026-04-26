{{-- MOBILE RESPONSIVE: kitchen-display.blade.php --}}
<div wire:poll.10s>
    <div class="mb-4 md:mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h2 class="text-xl md:text-2xl font-bold text-gray-100">شاشة المطبخ (KDS)</h2>
        <span class="px-4 py-2 w-full md:w-auto text-center bg-surface border border-[#2A2A2A] rounded-lg text-gray-400 text-sm">
            تحديث تلقائي (10 ثواني)
        </span>
    </div>

    @if($orders->isEmpty())
        <div class="h-64 flex items-center justify-center text-gray-500 bg-surface border border-[#2A2A2A] rounded-2xl p-6 text-center">
            لا توجد طلبات نشطة. المطبخ جاهز!
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4 md:gap-6">
            @foreach($orders as $order)
                <div class="bg-surface border {{ $order->status === 'preparing' ? 'border-amber-500 shadow-lg shadow-amber-500/10' : 'border-[#2A2A2A]' }} rounded-2xl flex flex-col overflow-hidden transition-all h-[500px]">
                    {{-- Order Header --}}
                    <div class="p-4 bg-elevated border-b border-[#2A2A2A] flex justify-between items-start shrink-0">
                        <div>
                            <h3 class="font-bold text-gray-100 text-xl">#{{ $order->order_number }}</h3>
                            <div class="flex flex-wrap gap-2 mt-1">
                                <span class="text-xs font-bold px-2 py-1 bg-surface border border-[#2A2A2A] rounded-md text-gray-300">{{ $order->type === 'dine_in' ? 'محلي' : ($order->type === 'takeaway' ? 'سفري' : 'توصيل') }}</span>
                                @if($order->table_number)
                                    <span class="text-xs px-2 py-1 bg-amber-500/10 border border-amber-500/20 rounded-md text-amber-500 font-bold">طاولة {{ $order->table_number }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right flex flex-col items-end">
                            <span class="text-xs font-bold text-gray-400 bg-black/20 px-2 py-1 rounded-lg border border-[#2A2A2A]">{{ $order->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    {{-- Order Items --}}
                    <div class="p-4 flex-1 overflow-y-auto space-y-3 bg-base">
                        @foreach($order->items as $item)
                            <div class="flex items-center justify-between p-3 rounded-xl border border-[#2A2A2A] {{ $item->status === 'ready' ? 'opacity-50' : 'bg-surface' }}">
                                <div class="flex gap-3">
                                    <span class="font-black text-amber-500 text-xl">{{ $item->quantity }}x</span>
                                    <div>
                                        <div class="font-bold text-base text-gray-100 {{ $item->status === 'ready' ? 'line-through text-gray-500' : '' }}">{{ $item->product->name }}</div>
                                        @if($item->notes)
                                            <div class="text-sm font-medium text-red-400 mt-1 bg-red-500/10 px-2 py-1 rounded border border-red-500/20">ملاحظة: {{ $item->notes }}</div>
                                        @endif
                                    </div>
                                </div>
                                @if($item->status !== 'ready')
                                    <button wire:click="markItemReady({{ $item->id }})" class="min-h-[48px] min-w-[48px] flex items-center justify-center rounded-xl bg-elevated border border-[#2A2A2A] text-emerald-500 hover:bg-emerald-500 hover:text-white transition-colors active:scale-95 shadow-lg">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Order Actions --}}
                    <div class="p-4 border-t border-[#2A2A2A] shrink-0 bg-surface">
                        @if($order->status === 'pending')
                            <button wire:click="markOrderPreparing({{ $order->id }})" class="w-full min-h-[56px] rounded-xl bg-amber-500 text-black text-lg font-black hover:bg-amber-400 transition-colors active:scale-95 shadow-lg shadow-amber-500/20">
                                بدء التحضير
                            </button>
                        @elseif($order->status === 'preparing')
                            <button wire:click="markOrderReady({{ $order->id }})" class="w-full min-h-[56px] rounded-xl bg-emerald-500 text-white text-lg font-black hover:bg-emerald-400 transition-colors active:scale-95 shadow-lg shadow-emerald-500/20">
                                الطلب جاهز
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
{{-- 
  CHANGES:
  - Ensured active buttons inside items list have min-h-[48px] min-w-[48px] for touch precision.
  - Boosted main action buttons at the bottom to min-h-[56px] text-lg.
  - Allowed header and live tracker widget to gracefully stack on mobile via flex-col.
--}}
