<div>
    <div class="mb-6 flex justify-between items-center flex-wrap gap-4">
        <h2 class="text-2xl font-bold text-gray-100">تقرير الأرباح</h2>
        
        <div class="flex gap-4">
            <input type="date" wire:model.live="dateFrom" class="bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500">
            <span class="text-gray-400 self-center">إلى</span>
            <input type="date" wire:model.live="dateTo" class="bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500">
            <button wire:click="export" class="px-4 py-2 bg-amber-500 text-black font-bold rounded-xl hover:bg-amber-400 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                تصدير Excel
            </button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-6">
            <h3 class="text-gray-400 font-medium mb-1">إجمالي المبيعات</h3>
            <p class="text-3xl font-bold text-emerald-400">${{ number_format($totals['sales'], 2) }}</p>
        </div>
        <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-6">
            <h3 class="text-gray-400 font-medium mb-1">تكلفة البضاعة (COGS)</h3>
            <p class="text-3xl font-bold text-red-400">-${{ number_format($totals['cogs'], 2) }}</p>
        </div>
        <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-6">
            <h3 class="text-gray-400 font-medium mb-1">المصروفات</h3>
            <p class="text-3xl font-bold text-red-400">-${{ number_format($totals['expenses'], 2) }}</p>
        </div>
        <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-6">
            <h3 class="text-gray-400 font-medium mb-1">الهالك</h3>
            <p class="text-3xl font-bold text-amber-500">-${{ number_format($totals['wastage'], 2) }}</p>
        </div>
        <div class="bg-surface border {{ $totals['net_profit'] >= 0 ? 'border-emerald-500/50 shadow-lg shadow-emerald-500/10' : 'border-red-500/50 shadow-lg shadow-red-500/10' }} rounded-2xl p-6">
            <h3 class="text-gray-400 font-medium mb-1">صافي الربح</h3>
            <p class="text-3xl font-bold {{ $totals['net_profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                {{ $totals['net_profit'] >= 0 ? '+' : '' }}${{ number_format($totals['net_profit'], 2) }}
            </p>
        </div>
    </div>

    <!-- Daily Breakdown -->
    <div class="bg-surface border border-[#2A2A2A] rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-[#2A2A2A]">
            <h3 class="text-lg font-bold text-gray-100">التفصيل اليومي</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-base border-b border-[#2A2A2A]">
                    <tr>
                        <th class="p-4 text-gray-400 font-medium">التاريخ</th>
                        <th class="p-4 text-gray-400 font-medium text-left">الطلبات</th>
                        <th class="p-4 text-gray-400 font-medium text-left">المبيعات</th>
                        <th class="p-4 text-gray-400 font-medium text-left">COGS</th>
                        <th class="p-4 text-gray-400 font-medium text-left">المصروفات</th>
                        <th class="p-4 text-gray-400 font-medium text-left">الهالك</th>
                        <th class="p-4 text-gray-400 font-medium text-left">صافي الربح</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2A2A2A]">
                    @forelse($days as $day)
                        @if($day['orders_count'] > 0 || $day['expenses'] > 0 || $day['wastage'] > 0)
                        <tr class="hover:bg-elevated transition-colors">
                            <td class="p-4 text-gray-100 font-medium">{{ Carbon\Carbon::parse($day['date'])->format('M d, Y') }}</td>
                            <td class="p-4 text-gray-300 text-left">{{ $day['orders_count'] }}</td>
                            <td class="p-4 text-emerald-400 font-medium text-left">${{ number_format($day['sales'], 2) }}</td>
                            <td class="p-4 text-red-400 text-left">-${{ number_format($day['cogs'], 2) }}</td>
                            <td class="p-4 text-red-400 text-left">-${{ number_format($day['expenses'], 2) }}</td>
                            <td class="p-4 text-amber-500 text-left">-${{ number_format($day['wastage'], 2) }}</td>
                            <td class="p-4 font-bold text-left {{ $day['net_profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
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
</div>
