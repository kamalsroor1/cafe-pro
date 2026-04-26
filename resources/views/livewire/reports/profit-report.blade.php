{{-- MOBILE RESPONSIVE: profit-report.blade.php --}}
<div>
    <div class="mb-4 lg:mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <h2 class="text-xl md:text-2xl font-bold text-gray-100">تقرير الأرباح</h2>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <input type="date" wire:model.live="dateFrom" class="min-h-[48px] flex-1 sm:w-auto bg-surface border border-[#2A2A2A] rounded-xl px-2 md:px-4 py-2 text-gray-100 focus:border-amber-500 text-sm md:text-base">
                <span class="text-gray-400 text-sm">إلى</span>
                <input type="date" wire:model.live="dateTo" class="min-h-[48px] flex-1 sm:w-auto bg-surface border border-[#2A2A2A] rounded-xl px-2 md:px-4 py-2 text-gray-100 focus:border-amber-500 text-sm md:text-base">
            </div>
            
            <button wire:click="export" class="min-h-[48px] px-6 py-2 w-full sm:w-auto bg-amber-500/10 text-amber-500 border border-amber-500/20 font-bold rounded-xl hover:bg-amber-500/20 transition-colors flex items-center justify-center gap-2 active:scale-95">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                تصدير Excel
            </button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 md:gap-4 lg:gap-6 mb-6 md:mb-8">
        <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-4 md:p-6 col-span-2 lg:col-span-1">
            <h3 class="text-sm md:text-base text-gray-400 font-medium mb-1">إجمالي المبيعات</h3>
            <p class="text-2xl md:text-3xl font-bold text-emerald-400">${{ number_format($totals['sales'], 2) }}</p>
        </div>
        <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-4 md:p-6 col-span-1">
            <h3 class="text-sm md:text-base text-gray-400 font-medium mb-1">COGS</h3>
            <p class="text-xl md:text-3xl font-bold text-red-400">-${{ number_format($totals['cogs'], 2) }}</p>
        </div>
        <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-4 md:p-6 col-span-1">
            <h3 class="text-sm md:text-base text-gray-400 font-medium mb-1">المصروفات</h3>
            <p class="text-xl md:text-3xl font-bold text-red-400">-${{ number_format($totals['expenses'], 2) }}</p>
        </div>
        <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-4 md:p-6 col-span-1">
            <h3 class="text-sm md:text-base text-gray-400 font-medium mb-1">الهالك</h3>
            <p class="text-xl md:text-3xl font-bold text-amber-500">-${{ number_format($totals['wastage'], 2) }}</p>
        </div>
        <div class="bg-surface border {{ $totals['net_profit'] >= 0 ? 'border-emerald-500/50 shadow-lg shadow-emerald-500/10' : 'border-red-500/50 shadow-lg shadow-red-500/10' }} rounded-2xl p-4 md:p-6 col-span-2 lg:col-span-1 relative overflow-hidden flex items-center justify-between lg:block">
            <div class="absolute -right-4 -top-4 w-20 h-20 blur-xl opacity-20 {{ $totals['net_profit'] >= 0 ? 'bg-emerald-500' : 'bg-red-500' }}"></div>
            <div>
                <h3 class="text-sm md:text-base text-gray-400 font-bold mb-1">صافي الربح</h3>
                <p class="text-2xl md:text-3xl font-black {{ $totals['net_profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                    {{ $totals['net_profit'] >= 0 ? '+' : '' }}${{ number_format($totals['net_profit'], 2) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Desktop Daily Breakdown -->
    <div class="hidden md:block bg-surface border border-[#2A2A2A] rounded-2xl overflow-hidden">
        <div class="p-4 md:p-6 border-b border-[#2A2A2A]">
            <h3 class="text-lg font-bold text-gray-100">التفصيل اليومي</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right whitespace-nowrap">
                <thead class="bg-base border-b border-[#2A2A2A]">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-gray-400">التاريخ</th>
                        <th class="p-4 text-sm font-semibold text-gray-400 text-left">الطلبات</th>
                        <th class="p-4 text-sm font-semibold text-gray-400 text-left">المبيعات</th>
                        <th class="p-4 text-sm font-semibold text-gray-400 text-left">COGS</th>
                        <th class="p-4 text-sm font-semibold text-gray-400 text-left">المصروفات</th>
                        <th class="p-4 text-sm font-semibold text-gray-400 text-left">الهالك</th>
                        <th class="p-4 text-sm font-semibold text-gray-400 text-left">صافي الربح</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2A2A2A]">
                    @forelse($days as $day)
                        @if($day['orders_count'] > 0 || $day['expenses'] > 0 || $day['wastage'] > 0)
                        <tr class="hover:bg-elevated transition-colors">
                            <td class="p-4 text-gray-100 font-medium">{{ Carbon\Carbon::parse($day['date'])->format('M d, Y') }}</td>
                            <td class="p-4 text-gray-300 text-left font-medium">{{ $day['orders_count'] }}</td>
                            <td class="p-4 text-emerald-400 font-bold text-left">${{ number_format($day['sales'], 2) }}</td>
                            <td class="p-4 text-red-400 text-left">-${{ number_format($day['cogs'], 2) }}</td>
                            <td class="p-4 text-red-400 text-left">-${{ number_format($day['expenses'], 2) }}</td>
                            <td class="p-4 text-amber-500 text-left">-${{ number_format($day['wastage'], 2) }}</td>
                            <td class="p-4 font-black text-left {{ $day['net_profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                {{ $day['net_profit'] >= 0 ? '+' : '' }}${{ number_format($day['net_profit'], 2) }}
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400">لا توجد بيانات لهذا النطاق الزمني.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Mobile Daily Breakdown Cards -->
    <div class="md:hidden">
        <h3 class="text-lg font-bold text-gray-100 mb-3 ml-1">التفصيل اليومي</h3>
        <div class="space-y-3">
            @forelse($days as $day)
                @if($day['orders_count'] > 0 || $day['expenses'] > 0 || $day['wastage'] > 0)
                <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-4 relative overflow-hidden">
                    <div class="flex justify-between items-end mb-3 border-b border-[#2A2A2A] pb-3">
                        <div class="flex-1">
                            <span class="text-sm font-bold text-gray-400">{{ Carbon\Carbon::parse($day['date'])->format('M d, Y') }}</span>
                            <div class="text-lg font-black mt-1 {{ $day['net_profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                {{ $day['net_profit'] >= 0 ? '+' : '' }}${{ number_format($day['net_profit'], 2) }} صافي
                            </div>
                        </div>
                        <div class="text-left">
                            <span class="block text-xs text-gray-500">الطلبات</span>
                            <span class="font-bold text-gray-100">{{ $day['orders_count'] }}</span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="flex justify-between bg-base p-2 rounded-lg border border-[#2A2A2A]">
                            <span class="text-gray-400">مبيعات</span>
                            <span class="text-emerald-400 font-bold">${{ number_format($day['sales'], 2) }}</span>
                        </div>
                        <div class="flex justify-between bg-base p-2 rounded-lg border border-[#2A2A2A]">
                            <span class="text-gray-400">COGS</span>
                            <span class="text-red-400 font-bold">-${{ number_format($day['cogs'], 2) }}</span>
                        </div>
                        <div class="flex justify-between bg-base p-2 rounded-lg border border-[#2A2A2A]">
                            <span class="text-gray-400">مصروفات</span>
                            <span class="text-red-400 font-bold">-${{ number_format($day['expenses'], 2) }}</span>
                        </div>
                        <div class="flex justify-between bg-base p-2 rounded-lg border border-[#2A2A2A]">
                            <span class="text-gray-400">هالك</span>
                            <span class="text-amber-500 font-bold">-${{ number_format($day['wastage'], 2) }}</span>
                        </div>
                    </div>
                </div>
                @endif
            @empty
                <div class="bg-surface rounded-2xl p-8 border border-[#2A2A2A] text-center text-gray-500">
                    لا توجد بيانات لهذا النطاق الزمني.
                </div>
            @endforelse
        </div>
    </div>
</div>
{{-- 
  CHANGES:
  - Flex inputs properly wrap on mobile. Use min-h-[48px] minimum thresholds.
  - Export Button fills col width on tiny screens.
  - Table transitioned to Cards structure explicitly built inside the specific foreach loops targeting "md:hidden" vs "md:block".
--}}
