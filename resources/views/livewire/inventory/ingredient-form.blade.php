<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-surface border border-[#2A2A2A] rounded-2xl w-full max-w-lg p-6 shadow-2xl">
            <h3 class="text-xl font-bold text-gray-100 mb-6">{{ $ingredient_id ? 'تعديل مكون' : 'مكون جديد' }}</h3>
            
            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300">الاسم</label>
                    <input wire:model="name" type="text" class="mt-1 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                    @error('name') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300">الوحدة</label>
                        <select wire:model="unit" class="mt-1 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                            <option value="kg">كجم</option>
                            <option value="g">جم</option>
                            <option value="l">لتر</option>
                            <option value="ml">مل</option>
                            <option value="pcs">قطعة</option>
                            <option value="tbsp">ملعقة كبيرة</option>
                            <option value="tsp">ملعقة صغيرة</option>
                        </select>
                        @error('unit') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">التكلفة / الوحدة</label>
                        <input wire:model="cost_per_unit" type="number" step="0.0001" class="mt-1 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                        @error('cost_per_unit') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300">الكمية الحالية</label>
                        <input wire:model="stock_qty" type="number" step="0.001" class="mt-1 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                        @error('stock_qty') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">حد التنبيه</label>
                        <input wire:model="min_stock_qty" type="number" step="0.001" class="mt-1 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                        @error('min_stock_qty') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300">المورد (اختياري)</label>
                    <input wire:model="supplier" type="text" class="mt-1 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                    @error('supplier') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-xl text-gray-400 hover:text-gray-100 hover:bg-elevated transition-colors">
                        إلغاء
                    </button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-amber-500 text-black font-bold hover:bg-amber-400 transition-colors flex items-center">
                        <span wire:loading.remove>حفظ</span>
                        <span wire:loading>جاري الحفظ...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
