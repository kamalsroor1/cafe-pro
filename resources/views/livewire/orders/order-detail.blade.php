{{-- MOBILE RESPONSIVE: order-detail.blade.php --}}
<div>
    <div class="mb-4 md:mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <a href="{{ route('orders.index') }}" class="min-w-[48px] min-h-[48px] bg-surface border border-[#2A2A2A] rounded-xl flex flex-shrink-0 items-center justify-center text-gray-400 hover:text-gray-100 transition-colors active:scale-95 shadow-sm">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="flex flex-wrap items-center gap-2 md:gap-4">
                <h2 class="text-xl md:text-2xl font-bold text-gray-100">طلب #{{ $order->order_number }}</h2>
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
            </div>
        </div>
        
        <div class="w-full md:w-auto">
            <a href="{{ route('orders.receipt', $order) }}" target="_blank" class="min-h-[48px] w-full md:w-auto px-6 py-2 bg-amber-500 text-black font-bold flex items-center justify-center gap-2 rounded-xl border border-transparent hover:bg-amber-400 transition-colors active:scale-95 shadow-lg shadow-amber-500/20">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4h10z"></path></svg>
                طباعة الفاتورة
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-4 md:p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-100 mb-4 px-2">العناصر</h3>
                <div class="space-y-3">
                    @foreach($order->items as $item)
                        <div class="flex justify-between items-start md:items-center bg-base p-3 md:p-4 rounded-xl border border-[#2A2A2A]">
                            <div class="flex items-start gap-3">
                                <span class="font-black text-amber-500 text-lg">{{ $item->quantity }}x</span>
                                <div>
                                    <p class="text-gray-100 font-bold text-base md:text-lg">{{ $item->product->name }}</p>
                                    @if($item->addons)
                                        <p class="text-sm font-medium text-amber-500/80 mt-1 flex items-center gap-1">
                                           <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg> يحتوي على إضافات
                                        </p>
                                    @endif
                                    @if($item->notes)
                                        <p class="text-sm text-gray-400 mt-1">{{ $item->notes }}</p>
                                    @endif
                                </div>
                            </div>
                            <span class="text-gray-100 font-black text-lg">${{ number_format($item->subtotal, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-4 md:p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-100 mb-4 px-2">المدفوعات</h3>
                <div class="space-y-3">
                    @forelse($order->payments as $payment)
                        <div class="flex justify-between items-center bg-base p-3 md:p-4 rounded-xl border border-[#2A2A2A]">
                            <div>
                                @php
                                    $methodAr = ['cash' => 'نقدي', 'card' => 'بطاقة'];
                                    $paymentMethod = $payment->method->value ?? $payment->method;
                                @endphp
                                <p class="text-gray-100 font-bold text-base capitalize flex items-center gap-2">
                                    <span class="p-1 rounded bg-elevated border border-[#2A2A2A] text-amber-500">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </span>
                                    {{ $methodAr[$paymentMethod] ?? $paymentMethod }}
                                </p>
                                <p class="text-xs font-medium text-gray-400 mt-2">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <span class="text-emerald-400 font-black text-lg">${{ number_format($payment->amount, 2) }}</span>
                        </div>
                    @empty
                        <div class="text-center p-6 border border-dashed border-[#2A2A2A] rounded-xl text-gray-400 font-medium">
                            لا توجد مدفوعات مسجلة حتى الآن.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <div class="space-y-6">
            <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-4 md:p-6 shadow-sm relative overflow-hidden">
                <div class="absolute right-0 top-0 w-32 h-32 bg-amber-500/5 rounded-full blur-3xl"></div>
                <h3 class="text-lg font-bold text-gray-100 mb-4 px-2 relative z-10">الملخص المالي</h3>
                
                <div class="bg-base border border-[#2A2A2A] rounded-xl p-4 space-y-3 relative z-10">
                    <div class="flex justify-between items-center text-sm md:text-base font-medium text-gray-400">
                        <span>المجموع الفرعي</span>
                        <span>${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm md:text-base font-medium text-gray-400">
                        <span>الضريبة المضافة</span>
                        <span>${{ number_format($order->tax, 2) }}</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="flex justify-between items-center text-sm md:text-base font-medium text-amber-500">
                        <span>الخصم</span>
                        <span>-${{ number_format($order->discount, 2) }}</span>
                    </div>
                    @endif
                    <div class="pt-4 mt-4 border-t border-[#2A2A2A] flex justify-between items-center text-gray-100 font-bold">
                        <span class="text-lg">الإجمالي</span>
                        <span class="text-3xl font-black text-amber-500">${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-4 md:p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-100 mb-4 px-2">معلومات الطلب</h3>
                <div class="bg-base border border-[#2A2A2A] rounded-xl p-4 space-y-4">
                    <div class="flex justify-between">
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-medium text-gray-500">تاريخ الطلب</span>
                            <span class="text-sm font-bold text-gray-100">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                    <div class="h-px bg-[#2A2A2A] w-full"></div>
                    <div class="flex justify-between">
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-medium text-gray-500">نوع الطلب</span>
                            @php
                                $typeAr = ['dine_in' => 'محلي', 'takeaway' => 'سفري', 'delivery' => 'توصيل'];
                                $orderType = str_replace('_', ' ', $order->type->value ?? $order->type);
                            @endphp
                            <span class="text-sm font-bold text-amber-500 capitalize">{{ $typeAr[$order->type->value ?? $order->type] ?? $orderType }}</span>
                        </div>
                    </div>
                    
                    @if($order->table_number)
                    <div class="h-px bg-[#2A2A2A] w-full"></div>
                    <div class="flex justify-between">
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-medium text-gray-500">رقم الطاولة</span>
                            <span class="text-sm font-bold text-gray-100">{{ $order->table_number }}</span>
                        </div>
                    </div>
                    @endif
                    
                    <div class="h-px bg-[#2A2A2A] w-full"></div>
                    <div class="flex justify-between">
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-medium text-gray-500">معرف الشفت</span>
                            <span class="text-sm font-bold text-gray-100">#{{ $order->shift_id }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- 
  CHANGES:
  - Flex headers updated to nicely stack button onto next row spanning w-full.
  - Formatted order items and payments dynamically parsing out separate block chunks `bg-base` vs raw borders.
  - Made text hierarchy easily scannable and fat fingers capable on smaller device areas.
--}}
