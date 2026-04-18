<div>
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-100">المكونات (المخزون)</h2>
        
        <div class="flex gap-4">
            <input wire:model.live="search" type="text" placeholder="بحث في المكونات..." 
                class="bg-surface border border-[#2A2A2A] rounded-lg px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
            
            <button onclick="Livewire.dispatchTo('inventory.wastage-form', 'openWastageModal')" class="bg-red-500/20 text-red-400 px-4 py-2 rounded-lg font-semibold hover:bg-red-500/30 transition-colors">
                تسجيل هالك
            </button>
            <button onclick="Livewire.dispatchTo('inventory.ingredient-form', 'openModal')" class="bg-amber-500 text-black px-4 py-2 rounded-lg font-semibold hover:bg-amber-400 transition-colors">
                + مكون جديد
            </button>
        </div>
    </div>

    <div class="bg-surface rounded-xl border border-[#2A2A2A] overflow-hidden">
        <table class="w-full text-right">
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
                        {{ number_format($ingredient->stock_qty, 3) }} {{ $ingredient->unit }}
                    </td>
                    <td class="p-4 text-gray-400">{{ number_format($ingredient->min_stock_qty, 3) }} {{ $ingredient->unit }}</td>
                    <td class="p-4 text-gray-300">${{ number_format($ingredient->cost_per_unit, 2) }}</td>
                    <td class="p-4 text-gray-400">{{ $ingredient->supplier ?? '-' }}</td>
                    <td class="p-4">
                        <div class="flex gap-2">
                            <button onclick="Livewire.dispatchTo('inventory.ingredient-form', 'openModal', { id: {{ $ingredient->id }} })" class="p-1 text-gray-400 hover:text-amber-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button wire:click="delete({{ $ingredient->id }})" class="p-1 text-gray-400 hover:text-red-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="p-4 border-t border-[#2A2A2A]">
            {{ $ingredients->links() }}
        </div>
    </div>

    @livewire('inventory.ingredient-form')
    @livewire('inventory.wastage-form')
</div>
