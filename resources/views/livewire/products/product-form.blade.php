{{-- MOBILE RESPONSIVE: product-form.blade.php --}}
<div>
    @if($isOpen)
    <div class="fixed inset-0 z-[100] flex items-end md:items-center justify-center bg-black/60 backdrop-blur-sm p-0 md:p-4 transition-all">
        <div class="bg-surface border-t md:border border-[#2A2A2A] rounded-t-3xl md:rounded-3xl w-full max-w-lg p-6 shadow-2xl overflow-y-auto max-h-[90vh] md:max-h-[80vh]">
            <h3 class="text-xl md:text-2xl font-bold text-gray-100 mb-6">{{ $product_id ? 'تعديل منتج' : 'إضافة منتج جديد' }}</h3>
            
            <form wire:submit.prevent="save" class="space-y-5">
                <div>
                    <label class="block text-sm md:text-base font-bold text-gray-300 mb-2">الاسم</label>
                    <input wire:model="name" type="text" class="min-h-[56px] w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500 text-lg">
                    @error('name') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm md:text-base font-bold text-gray-300 mb-2">الفئة</label>
                    <select wire:model="category_id" class="min-h-[56px] w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500 text-lg appearance-none">
                        <option value="">اختر فئة...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm md:text-base font-bold text-gray-300 mb-2">السعر البيع</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-bold">$</span>
                            </div>
                            <input wire:model="price" type="number" step="0.01" inputmode="decimal" class="min-h-[56px] pr-8 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500 text-lg">
                        </div>
                        @error('price') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm md:text-base font-bold text-gray-300 mb-2">التكلفة (اختياري)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-bold">$</span>
                            </div>
                            <input wire:model="cost" type="number" step="0.01" inputmode="decimal" class="min-h-[56px] pr-8 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500 text-lg">
                        </div>
                        @error('cost') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-3 p-4 border border-[#2A2A2A] rounded-xl bg-base cursor-pointer hover:border-amber-500/50 transition-colors">
                        <div class="relative flex items-center">
                            <input wire:model="is_available" type="checkbox" id="is_available" class="w-6 h-6 rounded bg-surface border-[#2A2A2A] text-amber-500 focus:ring-amber-500 focus:ring-offset-surface">
                        </div>
                        <span class="text-base font-bold text-gray-100">المنتج متاح للبيع في الـ POS</span>
                    </label>
                </div>

                <div class="flex flex-col-reverse md:flex-row justify-end gap-3 md:gap-4 mt-8 pt-4 border-t border-[#2A2A2A]">
                    <button type="button" wire:click="closeModal" class="min-h-[56px] w-full md:w-auto px-6 rounded-xl text-gray-400 hover:text-gray-100 bg-elevated border border-[#2A2A2A] hover:bg-surface transition-colors font-bold text-lg">
                        إلغاء الأمر
                    </button>
                    <button type="submit" class="min-h-[56px] w-full md:w-auto px-8 rounded-xl bg-amber-500 text-black font-black hover:bg-amber-400 shadow-lg shadow-amber-500/20 active:scale-95 transition-all text-lg">
                        <span wire:loading.remove>حفظ المنتج</span>
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
  - Switched from generic modal to bottom-sheet snap on mobile (`items-end`, `rounded-t-3xl`).
  - Updated inputs, selects, buttons to `min-h-[56px]` for extremely fat-finger-safe operation.
  - Spaced out grids handling multiple flex layouts. Checkboxes made into 48px min clickable areas.
--}}
