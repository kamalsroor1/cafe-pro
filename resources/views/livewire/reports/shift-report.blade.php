<div>
    <div class="mb-6 flex justify-between items-center flex-wrap gap-4">
        <h2 class="text-2xl font-bold text-gray-100">تقرير الشفتات</h2>
        
        <div class="flex gap-4">
            <input type="date" wire:model.live="dateFrom" class="bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500">
            <span class="text-gray-400 self-center">إلى</span>
            <input type="date" wire:model.live="dateTo" class="bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500">
        </div>
    </div>

    <div class="bg-surface border border-[#2A2A2A] rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-base border-b border-[#2A2A2A]">
                    <tr>
                        <th class="p-4 text-gray-400 font-medium">الشفت / المستخدم</th>
                        <th class="p-4 text-gray-400 font-medium">الوقت</th>
                        <th class="p-4 text-gray-400 font-medium text-left">الطلبات</th>
                        <th class="p-4 text-gray-400 font-medium text-left">المبيعات</th>
                        <th class="p-4 text-gray-400 font-medium text-left">النقدية المتوقعة</th>
                        <th class="p-4 text-gray-400 font-medium text-left">النقدية الفعلية</th>
                        <th class="p-4 text-gray-400 font-medium text-left">الفرق</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2A2A2A]">
                    @forelse($shifts as $shift)
                        @php $summary = $summaries[$shift->id]; @endphp
                        <tr class="hover:bg-elevated transition-colors">
                            <td class="p-4">
                                <div class="font-medium text-gray-100">#{{ $shift->id }}</div>
                                <div class="text-sm text-gray-400">{{ $shift->user->name }}</div>
                            </td>
                            <td class="p-4">
                                <div class="text-gray-100">{{ $shift->started_at->format('M d, h:i A') }}</div>
                                <div class="text-sm text-gray-400">
                                    {{ $shift->ended_at ? 'إلى ' . $shift->ended_at->format('h:i A') : 'مفتوح' }}
                                </div>
                            </td>
                            <td class="p-4 text-gray-300 text-left">{{ $summary['orders_count'] }}</td>
                            <td class="p-4 text-emerald-400 font-medium text-left">${{ number_format($summary['total_sales'], 2) }}</td>
                            <td class="p-4 text-gray-300 text-left">${{ number_format($summary['expected_cash'], 2) }}</td>
                            <td class="p-4 font-medium text-left text-gray-100">
                                {{ $shift->ended_at ? '$' . number_format($summary['actual_cash'], 2) : '-' }}
                            </td>
                            <td class="p-4 font-bold text-left">
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
                            <td colspan="7" class="p-8 text-center text-gray-400">لا توجد شفتات لهذا النطاق الزمني.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-6">
        {{ $shifts->links() }}
    </div>
</div>
