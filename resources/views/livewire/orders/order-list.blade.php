{{-- MOBILE RESPONSIVE: order-list.blade.php --}}
<div>
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h2 class="text-xl md:text-2xl font-bold text-gray-100">سجل الطلبات</h2>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <input type="date" wire:model.live="date" class="min-h-[48px] bg-surface w-full sm:w-auto border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
            <select wire:model.live="status" class="min-h-[48px] bg-surface w-full sm:w-auto border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500 appearance-none">
                <option value="">جميع الحالات</option>
                <option value="pending">قيد الانتظار</option>
                <option value="preparing">قيد التحضير</option>
                <option value="ready">جاهز</option>
                <option value="completed">مكتمل</option>
                <option value="cancelled">ملغي</option>
            </select>
        </div>
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden md:block bg-surface border border-[#2A2A2A] rounded-2xl overflow-x-auto">
        <table class="w-full text-right whitespace-nowrap">
            <thead class="bg-elevated border-b border-[#2A2A2A]">
                <tr>
                    <th class="p-4 text-gray-400 font-semibold text-sm">رقم الطلب #</th>
                    <th class="p-4 text-gray-400 font-semibold text-sm">التاريخ</th>
                    <th class="p-4 text-gray-400 font-semibold text-sm">النوع</th>
                    <th class="p-4 text-gray-400 font-semibold text-sm">الإجمالي</th>
                    <th class="p-4 text-gray-400 font-semibold text-sm">الحالة</th>
                    <th class="p-4 text-gray-400 font-semibold text-sm">الإجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#2A2A2A]">
                @foreach($orders as $order)
                    <tr class="hover:bg-elevated transition-colors">
                        <td class="p-4 text-gray-100 font-medium">#{{ $order->order_number }}</td>
                        <td class="p-4 text-gray-400">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                        <td class="p-4 text-gray-300 capitalize">{{ str_replace('_', ' ', $order->type->value ?? $order->type) }}</td>
                        <td class="p-4 text-emerald-400 font-bold">${{ number_format($order->total, 2) }}</td>
                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold border 
                                {{ $order->status === 'completed' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-red-500/20 text-red-400 border-red-500/30' : '' }}
                                {{ in_array($order->status, ['pending', 'preparing', 'ready']) ? 'bg-amber-500/20 text-amber-500 border-amber-500/30' : '' }}
                            ">
                                @php
                                    $statusAr = [
                                        'pending' => 'قيد الانتظار',
                                        'preparing' => 'قيد التحضير',
                                        'ready' => 'جاهز',
                                        'completed' => 'مكتمل',
                                        'cancelled' => 'ملغي',
                                    ];
                                @endphp
                                {{ $statusAr[$order->status->value ?? $order->status] ?? ($order->status->value ?? $order->status) }}
                            </span>
                        </td>
                        <td class="p-4">
                            <a href="{{ route('orders.show', $order->id) }}" class="text-amber-500 hover:text-amber-400 font-bold text-sm bg-amber-500/10 px-4 py-2 rounded-lg border border-amber-500/20 hover:bg-amber-500/20 transition-colors">عرض</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards View --}}
    <div class="md:hidden space-y-3">
        @foreach($orders as $order)
        <div class="bg-surface rounded-2xl p-4 border border-[#2A2A2A]">
            <div class="flex justify-between items-start mb-3">
                <div class="flex-1">
                    <p class="text-lg font-bold text-gray-100">#{{ $order->order_number }}</p>
                    <p class="text-sm text-gray-400 mt-1">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="text-emerald-400 font-bold text-lg">${{ number_format($order->total, 2) }}</span>
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold border whitespace-nowrap
                        {{ $order->status === 'completed' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : '' }}
                        {{ $order->status === 'cancelled' ? 'bg-red-500/20 text-red-400 border-red-500/30' : '' }}
                        {{ in_array($order->status, ['pending', 'preparing', 'ready']) ? 'bg-amber-500/20 text-amber-500 border-amber-500/30' : '' }}
                    ">
                        @php
                            $statusAr = [
                                'pending' => 'قيد الانتظار',
                                'preparing' => 'قيد التحضير',
                                'ready' => 'جاهز',
                                'completed' => 'مكتمل',
                                'cancelled' => 'ملغي',
                            ];
                        @endphp
                        {{ $statusAr[$order->status->value ?? $order->status] ?? ($order->status->value ?? $order->status) }}
                    </span>
                </div>
            </div>
            
            <div class="pt-3 border-t border-[#2A2A2A] flex items-center justify-between">
                <span class="text-sm text-gray-300 bg-elevated px-3 py-1.5 rounded-lg border border-[#2A2A2A]">
                    {{ str_replace('_', ' ', $order->type->value ?? $order->type) === 'dine in' ? 'محلي' : (str_replace('_', ' ', $order->type->value ?? $order->type) === 'takeaway' ? 'سفري' : 'توصيل') }}
                </span>
                
                <a href="{{ route('orders.show', $order->id) }}" class="min-h-[48px] px-6 inline-flex items-center justify-center font-bold text-amber-500 bg-amber-500/10 hover:bg-amber-500/20 rounded-xl transition-colors border border-amber-500/20 active:scale-95">
                    عرض الطلب
                </a>
            </div>
        </div>
        @endforeach
        
        @if(count($orders) === 0)
        <div class="bg-surface rounded-2xl p-8 border border-[#2A2A2A] text-center text-gray-500">
            لا توجد طلبات مسجلة بهذا التاريخ/الحالة.
        </div>
        @endif
    </div>
    
    <div class="mt-4 md:mt-6">
        {{ $orders->links() }}
    </div>
</div>
{{-- 
  CHANGES:
  - Repositioned header flex to be flex-col on mobile.
  - Added min-h-[48px] to select and date inputs for mobile ease-of-use.
  - Converted table into a md:block Desktop-only view.
  - Created a Mobile cards layout iterating order details nicely layered.
  - Ensure 'View' button is 48px touch optimized.
--}}
