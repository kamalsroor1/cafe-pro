{{-- MOBILE RESPONSIVE: shift-report.blade.php --}}
<div>
    <div class="mb-4 lg:mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <h2 class="text-xl md:text-2xl font-bold text-gray-100">تقرير الشفتات</h2>
        
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

    {{-- Desktop Table View --}}
    <div class="hidden md:block bg-surface border border-[#2A2A2A] rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right whitespace-nowrap">
                <thead class="bg-base border-b border-[#2A2A2A]">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-gray-400">الشفت / المستخدم</th>
                        <th class="p-4 text-sm font-semibold text-gray-400">الوقت</th>
                        <th class="p-4 text-sm font-semibold text-gray-400 text-left">الطلبات</th>
                        <th class="p-4 text-sm font-semibold text-gray-400 text-left">المبيعات</th>
                        <th class="p-4 text-sm font-semibold text-gray-400 text-left">النقدية المتوقعة</th>
                        <th class="p-4 text-sm font-semibold text-gray-400 text-left">النقدية الفعلية</th>
                        <th class="p-4 text-sm font-semibold text-gray-400 text-left">الفرق</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2A2A2A]">
                    @forelse($shifts as $shift)
                        @php $summary = $summaries[$shift->id]; @endphp
                        <tr class="hover:bg-elevated transition-colors">
                            <td class="p-4">
                                <div class="font-bold text-gray-100">شفت #{{ $shift->id }}</div>
                                <div class="text-sm font-medium text-amber-500 mt-1">{{ $shift->user->name }}</div>
                            </td>
                            <td class="p-4">
                                <div class="text-gray-100 font-medium">{{ $shift->started_at->format('M d, h:i A') }}</div>
                                <div class="text-sm text-gray-400 mt-1">
                                    {{ $shift->ended_at ? 'إلى ' . $shift->ended_at->format('h:i A') : 'مفتوح حالياً' }}
                                </div>
                            </td>
                            <td class="p-4 text-gray-300 font-bold text-left">{{ $summary['orders_count'] }}</td>
                            <td class="p-4 text-emerald-400 font-bold text-left">${{ number_format($summary['total_sales'], 2) }}</td>
                            <td class="p-4 text-gray-300 font-medium text-left">${{ number_format($summary['expected_cash'], 2) }}</td>
                            <td class="p-4 font-bold text-left text-gray-100">
                                {{ $shift->ended_at ? '$' . number_format($summary['actual_cash'], 2) : '-' }}
                            </td>
                            <td class="p-4 font-black text-left">
                                @if($shift->ended_at)
                                    <span class="{{ $summary['difference'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                        {{ $summary['difference'] > 0 ? '+' : '' }}${{ number_format($summary['difference'], 2) }}
                                    </span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400 font-medium">لا توجد شفتات لهذا النطاق الزمني.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- Mobile Cards View --}}
    <div class="md:hidden space-y-3">
        @forelse($shifts as $shift)
            @php $summary = $summaries[$shift->id]; @endphp
            <div class="bg-surface rounded-2xl p-4 border border-[#2A2A2A]">
                <div class="flex justify-between items-start mb-3 border-b border-[#2A2A2A] pb-3">
                    <div>
                        <span class="text-sm font-bold text-gray-100 flex items-center gap-2">
                            شفت #{{ $shift->id }}
                            @if(!$shift->ended_at)
                                <span class="bg-emerald-500/20 text-emerald-400 text-[10px] px-2 py-0.5 rounded-full border border-emerald-500/30">مفتوح</span>
                            @endif
                        </span>
                        <div class="text-xs text-amber-500 font-medium mt-1">{{ $shift->user->name }}</div>
                        <div class="text-[11px] text-gray-400 mt-1 mt-1.5 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $shift->started_at->format('M d, h:i A') }} {{ $shift->ended_at ? ' ← ' . $shift->ended_at->format('h:i A') : '' }}
                        </div>
                    </div>
                    <div class="text-left shrink-0">
                        <span class="block text-xs text-gray-500 mb-0.5">المبيعات</span>
                        <span class="font-bold text-emerald-400">${{ number_format($summary['total_sales'], 2) }}</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                    <div class="bg-base p-2 rounded-lg border border-[#2A2A2A]">
                        <span class="block text-xs text-gray-500">الطلبات المكتملة</span>
                        <span class="font-bold text-gray-100">{{ $summary['orders_count'] }}</span>
                    </div>
                    <div class="bg-base p-2 rounded-lg border border-[#2A2A2A]">
                        <span class="block text-xs text-gray-500">الدرج المتوقع</span>
                        <span class="font-bold text-gray-300">${{ number_format($summary['expected_cash'], 2) }}</span>
                    </div>
                </div>
                
                @if($shift->ended_at)
                <div class="flex justify-between items-center bg-elevated p-3 rounded-xl border border-[#2A2A2A]">
                    <div>
                        <span class="block text-[10px] text-gray-500">النقدية الفعلية</span>
                        <span class="font-bold text-gray-100">${{ number_format($summary['actual_cash'], 2) }}</span>
                    </div>
                    <div class="text-left">
                        <span class="block text-[10px] text-gray-500">العجز/الزيادة</span>
                        <span class="font-black {{ $summary['difference'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ $summary['difference'] > 0 ? '+' : '' }}${{ number_format($summary['difference'], 2) }}
                        </span>
                    </div>
                </div>
                @endif
            </div>
        @empty
            <div class="bg-surface rounded-2xl p-8 border border-[#2A2A2A] text-center text-gray-500 font-medium">
                لا توجد شفتات للمعايير المحددة.
            </div>
        @endforelse
    </div>

    <div class="mt-4 md:mt-6">
        {{ $shifts->links() }}
    </div>
</div>
{{-- 
  CHANGES:
  - Table to Mobile card restructuring hiding standard table on `md:hidden`.
  - Used nested stat blocks (`bg-base`) and explicit separation lines to maximize legibility. 
  - Adjusted global data pickers inputs layout mapping sizes (`min-h-[48px]`).
--}}
