<div>
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('orders.index') }}" class="w-10 h-10 bg-surface border border-[#2A2A2A] rounded-xl flex items-center justify-center text-gray-400 hover:text-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-100">طلب {{ $order->order_number }}</h2>
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
        </div>
        
        <div>
            <a href="{{ route('orders.receipt', $order) }}" target="_blank" class="px-4 py-2 bg-amber-500 text-black font-bold rounded-xl hover:bg-amber-400 transition-colors flex items-center gap-2 active:scale-95 transition-transform">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4h10z"></path></svg>
                طباعة الفاتورة
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-100 mb-4">العناصر</h3>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex justify-between items-center py-2 border-b border-[#2A2A2A] last:border-0">
                            <div>
                                <p class="text-gray-100 font-medium">{{ $item->quantity }}x {{ $item->product->name }}</p>
                                @if($item->addons)
                                    <p class="text-xs text-gray-400 mt-1">يحتوي على إضافات</p>
                                @endif
                            </div>
                            <span class="text-gray-100 font-bold">${{ number_format($item->subtotal, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-100 mb-4">المدفوعات</h3>
                <div class="space-y-4">
                    @forelse($order->payments as $payment)
                        <div class="flex justify-between items-center py-2 border-b border-[#2A2A2A] last:border-0">
                            <div>
                                @php
                                    $methodAr = ['cash' => 'نقدي', 'card' => 'بطاقة'];
                                    $paymentMethod = $payment->method->value ?? $payment->method;
                                @endphp
                                <p class="text-gray-100 font-medium capitalize">{{ $methodAr[$paymentMethod] ?? $paymentMethod }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <span class="text-emerald-400 font-bold">${{ number_format($payment->amount, 2) }}</span>
                        </div>
                    @empty
                        <p class="text-gray-400 italic">لا توجد مدفوعات مسجلة.</p>
                    @endforelse
                </div>
            </div>
        </div>
        
        <div class="space-y-6">
            <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-100 mb-4">الملخص</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-gray-400">
                        <span>المجموع الفرعي</span>
                        <span>${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-400">
                        <span>الضريبة</span>
                        <span>${{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-400">
                        <span>الخصم</span>
                        <span>-${{ number_format($order->discount, 2) }}</span>
                    </div>
                    <div class="pt-3 mt-3 border-t border-[#2A2A2A] flex justify-between items-center text-gray-100 font-bold text-lg">
                        <span>الإجمالي</span>
                        <span class="text-amber-500">${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-100 mb-4">التفاصيل</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">التاريخ</span>
                        <span class="text-gray-100">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">النوع</span>
                        @php
                            $typeAr = ['dine_in' => 'محلي', 'takeaway' => 'سفري', 'delivery' => 'توصيل'];
                            $orderType = str_replace('_', ' ', $order->type->value ?? $order->type);
                        @endphp
                        <span class="text-gray-100 capitalize">{{ $typeAr[$order->type->value ?? $order->type] ?? $orderType }}</span>
                    </div>
                    @if($order->table_number)
                    <div class="flex justify-between">
                        <span class="text-gray-400">الطاولة</span>
                        <span class="text-gray-100">{{ $order->table_number }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-400">رقم الشفت</span>
                        <span class="text-gray-100">#{{ $order->shift_id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
