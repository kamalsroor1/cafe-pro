{{-- MOBILE RESPONSIVE: ingredient-list.blade.php --}}
<div>
    <div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h2 class="text-xl md:text-2xl font-bold text-gray-100">المكونات (المخزون)</h2>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <input wire:model.live="search" type="text" placeholder="بحث في المكونات..." 
                class="min-h-[48px] bg-surface border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500 w-full sm:w-64">
            
            <div class="flex gap-2 w-full sm:w-auto">
                <button onclick="Livewire.dispatchTo('inventory.wastage-form', 'openWastageModal')" class="min-h-[48px] flex-1 sm:flex-none text-red-500 bg-red-500/10 hover:bg-red-500/20 px-4 py-2 rounded-xl border border-red-500/20 font-bold transition-colors active:scale-95 whitespace-nowrap">
                    تسجيل هالك
                </button>
                <button onclick="Livewire.dispatchTo('inventory.ingredient-form', 'openModal')" class="min-h-[48px] flex-1 sm:flex-none bg-amber-500 text-black px-4 py-2 rounded-xl font-bold hover:bg-amber-400 transition-colors shadow-lg shadow-amber-500/10 active:scale-95 whitespace-nowrap">
                    + مكون جديد
                </button>
            </div>
        </div>
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden md:block bg-surface rounded-2xl border border-[#2A2A2A] overflow-x-auto">
        <table class="w-full text-right whitespace-nowrap">
            <thead class="bg-elevated border-b border-[#2A2A2A]">
                <tr>
                    <th class="p-4 text-sm font-semibold text-gray-400">الاسم</th>
                    <th class="p-4 text-sm font-semibold text-gray-400">الكمية المتاحة</th>
                    <th class="p-4 text-sm font-semibold text-gray-400">حد التنبيه</th>
                    <th class="p-4 text-sm font-semibold text-gray-400">التكلفة/الوحدة</th>
                    <th class="p-4 text-sm font-semibold text-gray-400">المورد</th>
                    <th class="p-4 text-sm font-semibold text-gray-400">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#2A2A2A]">
                @foreach($ingredients as $ingredient)
                <tr class="hover:bg-elevated transition-colors">
                    <td class="p-4 text-gray-100 font-medium">{{ $ingredient->name }}</td>
                    <td class="p-4 font-bold {{ $ingredient->stock_qty <= $ingredient->min_stock_qty ? 'text-red-400' : 'text-emerald-400' }}">
                        {{ number_format($ingredient->stock_qty, 3) }} <span class="text-xs">{{ $ingredient->unit }}</span>
                    </td>
                    <td class="p-4 text-gray-400">{{ number_format($ingredient->min_stock_qty, 3) }} <span class="text-xs">{{ $ingredient->unit }}</span></td>
                    <td class="p-4 text-amber-500 font-bold">${{ number_format($ingredient->cost_per_unit, 2) }}</td>
                    <td class="p-4 text-gray-400">{{ $ingredient->supplier ?? '-' }}</td>
                    <td class="p-4">
                        <div class="flex gap-2">
                            <button onclick="Livewire.dispatchTo('inventory.ingredient-form', 'openModal', { id: {{ $ingredient->id }} })" class="p-2 min-w-[40px] min-h-[40px] flex items-center justify-center text-gray-400 hover:text-amber-500 hover:bg-amber-500/10 rounded-lg transition-colors border border-transparent hover:border-amber-500/20">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button wire:click="delete({{ $ingredient->id }})" class="p-2 min-w-[40px] min-h-[40px] flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-colors border border-transparent hover:border-red-500/20">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards View --}}
    <div class="md:hidden space-y-3">
        @foreach($ingredients as $ingredient)
        <div class="bg-surface rounded-2xl p-4 border border-[#2A2A2A]">
            <div class="flex justify-between items-start mb-3">
                <div class="flex-1">
                    <p class="text-base font-bold text-gray-100 line-clamp-1">{{ $ingredient->name }}</p>
                    <p class="text-sm text-gray-400 mt-1">{{ $ingredient->supplier ?? 'بدون مورد' }}</p>
                </div>
                <div class="flex flex-col items-end gap-1">
                    <span class="text-amber-500 font-bold">${{ number_format($ingredient->cost_per_unit, 2) }}/{{ $ingredient->unit }}</span>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3 mb-4 p-3 bg-elevated rounded-xl border border-[#2A2A2A]">
                <div>
                    <span class="block text-xs text-gray-500 mb-1">الكمية المتاحة</span>
                    <span class="font-black text-lg {{ $ingredient->stock_qty <= $ingredient->min_stock_qty ? 'text-red-400' : 'text-emerald-400' }}">
                        {{ number_format($ingredient->stock_qty, 2) }} <span class="text-xs">{{ $ingredient->unit }}</span>
                    </span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500 mb-1">حد التنبيه</span>
                    <span class="font-bold text-gray-300">
                        {{ number_format($ingredient->min_stock_qty, 2) }} <span class="text-xs">{{ $ingredient->unit }}</span>
                    </span>
                </div>
            </div>
            
            <div class="flex gap-2">
                <button
                    onclick="Livewire.dispatchTo('inventory.ingredient-form', 'openModal', { id: {{ $ingredient->id }} })"
                    class="flex-1 min-h-[48px] flex items-center justify-center gap-2 text-sm font-bold text-amber-500 bg-amber-500/10 hover:bg-amber-500/20 rounded-xl transition-colors border border-amber-500/20 active:scale-95">
                    تعديل
                </button>
                <button
                    wire:click="delete({{ $ingredient->id }})"
                    class="flex-1 min-h-[48px] flex items-center justify-center gap-2 text-sm font-bold text-red-500 bg-red-500/10 hover:bg-red-500/20 rounded-xl transition-colors border border-red-500/20 active:scale-95">
                    حذف
                </button>
            </div>
        </div>
        @endforeach
        
        @if(count($ingredients) === 0)
        <div class="bg-surface rounded-2xl p-8 border border-[#2A2A2A] text-center text-gray-500">
            لا توجد مكونات حالياً.
        </div>
        @endif
    </div>
        
    <div class="mt-4 md:mt-6 md:p-4 md:border-t md:border-[#2A2A2A] md:bg-surface md:rounded-b-2xl">
        {{ $ingredients->links() }}
    </div>

    @livewire('inventory.ingredient-form')
    @livewire('inventory.wastage-form')
</div>
{{-- 
  CHANGES:
  - Header actions stack gracefully using full width inputs/buttons.
  - Converted native table logic into `md:block` logic with separate mobile cards loop.
  - Nested stat blocks inside the item cards with distinct hierarchy (`bg-elevated`). 
  - Validated interactive dimensions (`min-h-[48px]`).
--}}
