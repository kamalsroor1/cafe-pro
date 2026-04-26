{{-- MOBILE RESPONSIVE: wastage-form.blade.php --}}
<div>
    @if($isOpen)
    <div class="fixed inset-0 z-[100] flex items-end md:items-center justify-center bg-black/60 backdrop-blur-sm p-0 md:p-4 transition-all">
        <div class="bg-surface border-t md:border border-[#2A2A2A] rounded-t-3xl md:rounded-3xl w-full max-w-md p-6 shadow-2xl overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center mb-6 border-b border-[#2A2A2A] pb-4">
                <h3 class="text-xl md:text-2xl font-bold text-gray-100">تسجيل هالك</h3>
                <button wire:click="closeModal" class="p-2 -mr-2 bg-base rounded-lg text-gray-400 hover:text-gray-100 hover:bg-elevated transition-colors active:scale-95">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-sm md:text-base font-bold text-gray-300 mb-2">المكون المهدر</label>
                    <select wire:model="ingredient_id" class="min-h-[56px] w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-lg text-gray-100 focus:border-amber-500 focus:ring-amber-500 appearance-none">
                        <option value="">اختر مكوناً...</option>
                        @foreach($availableIngredients as $ingredient)
                            <option value="{{ $ingredient->id }}">{{ $ingredient->name }} ({{ $ingredient->unit }})</option>
                        @endforeach
                    </select>
                    @error('ingredient_id') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm md:text-base font-bold text-gray-300 mb-2">الكمية المهدرة (بوحدة المكون)</label>
                    <input wire:model="qty_wasted" type="number" step="0.001" inputmode="decimal" class="min-h-[56px] w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-lg text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                    @error('qty_wasted') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm md:text-base font-bold text-gray-300 mb-2">السبب</label>
                    <select wire:model="reason" class="min-h-[56px] w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-lg text-gray-100 focus:border-amber-500 focus:ring-amber-500 appearance-none">
                        <option value="">اختر السبب...</option>
                        <option value="spillage">انسكاب / سقوط</option>
                        <option value="expired">منتهي الصلاحية</option>
                        <option value="damaged">تالف / تكسر</option>
                        <option value="other">أخرى</option>
                    </select>
                    @error('reason') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm md:text-base font-bold text-gray-300 mb-2">ملاحظات إضافية (اختياري)</label>
                    <textarea wire:model="notes" rows="3" class="w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-3 text-lg text-gray-100 focus:border-amber-500 focus:ring-amber-500"></textarea>
                    @error('notes') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex flex-col-reverse md:flex-row justify-end gap-3 md:gap-4 mt-8 pt-4 border-t border-[#2A2A2A]">
                    <button type="button" wire:click="closeModal" class="min-h-[56px] w-full md:w-auto px-6 rounded-xl text-gray-400 hover:text-gray-100 bg-elevated border border-[#2A2A2A] hover:bg-surface transition-colors font-bold text-lg">
                        إلغاء الأمر
                    </button>
                    <button type="submit" class="min-h-[56px] w-full md:w-auto px-8 rounded-xl bg-red-500 text-white font-black hover:bg-red-600 shadow-lg shadow-red-500/20 active:scale-95 transition-all text-lg flex items-center justify-center gap-2">
                        <span wire:loading.remove>تسجيل الهالك</span>
                        <span wire:loading>جاري التسجيل...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
{{-- 
  CHANGES:
  - Drawer modal sizing implementation (items-end rounded-t-3xl).
  - Explicit size boundaries for inputs (min-h-[56px] instead of default height + py-2).
  - Clearer labels + spacing modifications.
--}}
