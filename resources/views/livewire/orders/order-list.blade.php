<div>
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-100">سجل الطلبات</h2>
        
        <div class="flex gap-4">
            <input type="date" wire:model.live="date" class="bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
            <select wire:model.live="status" class="bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                <option value="">جميع الحالات</option>
                <option value="pending">قيد الانتظار</option>
                <option value="preparing">قيد التحضير</option>
                <option value="ready">جاهز</option>
                <option value="completed">مكتمل</option>
                <option value="cancelled">ملغي</option>
            </select>
        </div>
    </div>

    <div class="bg-surface border border-[#2A2A2A] rounded-2xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-base border-b border-[#2A2A2A]">
                <tr>
                    <th class="p-4 text-gray-400 font-medium">رقم الطلب #</th>
                    <th class="p-4 text-gray-400 font-medium">التاريخ</th>
                    <th class="p-4 text-gray-400 font-medium">النوع</th>
                    <th class="p-4 text-gray-400 font-medium">الإجمالي</th>
                    <th class="p-4 text-gray-400 font-medium">الحالة</th>
                    <th class="p-4 text-gray-400 font-medium">الإجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#2A2A2A]">
                @foreach($orders as $order)
                    <tr class="hover:bg-elevated transition-colors">
                        <td class="p-4 text-gray-100 font-medium">{{ $order->order_number }}</td>
                        <td class="p-4 text-gray-400">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                        <td class="p-4 text-gray-300 capitalize">{{ str_replace('_', ' ', $order->type->value ?? $order->type) }}</td>
                        <td class="p-4 text-emerald-400 font-bold">${{ number_format($order->total, 2) }}</td>
                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold 
                                {{ $order->status === 'completed' ? 'bg-emerald-500/20 text-emerald-400' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-red-500/20 text-red-400' : '' }}
                                {{ in_array($order->status, ['pending', 'preparing', 'ready']) ? 'bg-amber-500/20 text-amber-500' : '' }}
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
                            <a href="{{ route('orders.show', $order->id) }}" class="text-amber-500 hover:text-amber-400 font-medium">عرض</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-6">
        {{ $orders->links() }}
    </div>
</div>
