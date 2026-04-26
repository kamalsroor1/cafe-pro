{{-- MOBILE RESPONSIVE: ingredient-form.blade.php --}}
<div>
    @if($isOpen)
    <div class="fixed inset-0 z-[100] flex items-end md:items-center justify-center bg-black/60 backdrop-blur-sm p-0 md:p-4 transition-all">
        <div class="bg-surface border-t md:border border-[#2A2A2A] rounded-t-3xl md:rounded-3xl w-full max-w-lg p-6 shadow-2xl overflow-y-auto max-h-[90vh] md:max-h-[85vh]">
            <div class="flex justify-between items-center mb-6 border-b border-[#2A2A2A] pb-4">
                <h3 class="text-xl md:text-2xl font-bold text-gray-100">{{ $ingredient_id ? 'تعديل مكون' : 'إضافة مكون جديد' }}</h3>
                <button wire:click="closeModal" class="p-2 -mr-2 bg-base rounded-lg text-gray-400 hover:text-gray-100 hover:bg-elevated transition-colors active:scale-95">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-sm md:text-base font-bold text-gray-300 mb-2">الاسم</label>
                    <input wire:model="name" type="text" class="min-h-[56px] w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-lg text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                    @error('name') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm md:text-base font-bold text-gray-300 mb-2">الوحدة</label>
                        <select wire:model="unit" class="min-h-[56px] w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-lg text-gray-100 focus:border-amber-500 focus:ring-amber-500 appearance-none">
                            <option value="kg">كجم</option>
                            <option value="g">جم</option>
                            <option value="l">لتر</option>
                            <option value="ml">مل</option>
                            <option value="pcs">قطعة</option>
                            <option value="tbsp">ملعقة كبيرة</option>
                            <option value="tsp">ملعقة صغيرة</option>
                        </select>
                        @error('unit') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm md:text-base font-bold text-gray-300 mb-2">التكلفة / الوحدة</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-bold">$</span>
                            </div>
                            <input wire:model="cost_per_unit" type="number" step="0.0001" inputmode="decimal" class="min-h-[56px] pr-8 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-lg text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        @error('cost_per_unit') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm md:text-base font-bold text-gray-300 mb-2">الكمية الحالية</label>
                        <input wire:model="stock_qty" type="number" step="0.001" inputmode="decimal" class="min-h-[56px] w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-lg text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                        @error('stock_qty') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm md:text-base font-bold text-gray-300 mb-2">حد التنبيه</label>
                        <input wire:model="min_stock_qty" type="number" step="0.001" inputmode="decimal" class="min-h-[56px] w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-lg text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                        @error('min_stock_qty') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm md:text-base font-bold text-gray-300 mb-2">المورد (اختياري)</label>
                    <input wire:model="supplier" type="text" class="min-h-[56px] w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-lg text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                    @error('supplier') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex flex-col-reverse md:flex-row justify-end gap-3 md:gap-4 mt-8 pt-4 border-t border-[#2A2A2A]">
                    <button type="button" wire:click="closeModal" class="min-h-[56px] w-full md:w-auto px-6 rounded-xl text-gray-400 hover:text-gray-100 bg-elevated border border-[#2A2A2A] hover:bg-surface transition-colors font-bold text-lg">
                        إلغاء الأمر
                    </button>
                    <button type="submit" class="min-h-[56px] w-full md:w-auto px-8 rounded-xl bg-amber-500 text-black font-black hover:bg-amber-400 shadow-lg shadow-amber-500/20 active:scale-95 transition-all text-lg flex items-center justify-center gap-2">
                        <span wire:loading.remove>حفظ المكون</span>
                        <span wire:loading>جاري الحفظ...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
{{-- 
  CHANGES:
  - Used mobile-first bottom drawer layout (`items-end`, `rounded-t-3xl`).
  - Inputs inflated to 56px with nice text scaling (text-lg inside inputs).
  - Ensured correct numeric keyboard `inputmode="decimal"` on inputs where step is very granular.
--}}
