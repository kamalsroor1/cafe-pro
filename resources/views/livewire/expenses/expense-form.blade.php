{{-- MOBILE RESPONSIVE: expense-form.blade.php --}}
<div>
    @if($isOpen)
    <div class="fixed inset-0 z-[100] flex items-end md:items-center justify-center bg-black/60 backdrop-blur-sm p-0 md:p-4 transition-all">
        <div class="bg-surface border-t md:border border-[#2A2A2A] rounded-t-3xl md:rounded-3xl w-full max-w-md p-6 shadow-2xl relative overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center mb-6 border-b border-[#2A2A2A] pb-4">
                <h3 class="text-xl md:text-2xl font-bold text-gray-100">إضافة مصروف</h3>
                <button wire:click="closeModal" class="p-2 -mr-2 bg-base rounded-lg text-gray-400 hover:text-gray-100 hover:bg-elevated transition-colors active:scale-95">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form wire:submit.prevent="save" class="space-y-5">
                <div>
                    <label class="block text-sm md:text-base font-bold text-gray-300 mb-2">التاريخ</label>
                    <input wire:model="expenseDate" type="date" class="min-h-[56px] w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-lg text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                    @error('expenseDate') <span class="text-red-400 text-sm block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm md:text-base font-bold text-gray-300 mb-2">الفئة</label>
                    <select wire:model="categoryId" class="min-h-[56px] w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-lg text-gray-100 focus:border-amber-500 focus:ring-amber-500 appearance-none">
                        <option value="">اختر فئة...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('categoryId') <span class="text-red-400 text-sm block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm md:text-base font-bold text-gray-300 mb-2">المبلغ</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 font-bold">$</span>
                        </div>
                        <input wire:model="amount" type="number" step="0.01" inputmode="decimal" class="min-h-[56px] pr-8 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-lg text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    @error('amount') <span class="text-red-400 text-sm block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm md:text-base font-bold text-gray-300 mb-2">الوصف (اختياري)</label>
                    <textarea wire:model="description" rows="3" class="w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-3 text-lg text-gray-100 focus:border-amber-500 focus:ring-amber-500"></textarea>
                    @error('description') <span class="text-red-400 text-sm block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex flex-col-reverse md:flex-row justify-end gap-3 md:gap-4 mt-8 pt-4 border-t border-[#2A2A2A]">
                    <button type="button" wire:click="closeModal" class="min-h-[56px] w-full md:w-auto px-6 rounded-xl text-gray-400 hover:text-gray-100 bg-elevated border border-[#2A2A2A] hover:bg-surface transition-colors font-bold text-lg">
                        إلغاء الأمر
                    </button>
                    <button type="submit" class="min-h-[56px] w-full md:w-auto px-8 rounded-xl bg-amber-500 text-black font-black hover:bg-amber-400 transition-colors active:scale-95 flex items-center justify-center gap-2 text-lg shadow-lg shadow-amber-500/20">
                        <span wire:loading.remove>حفظ المصروف</span>
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
  - Upgraded inputs to min-h-[56px] suitable for simple touch layout.
  - Converted the modal to act as a proper drawer on mobile and popup on tablet.
  - Enforced stacked responsive flex layout on confirmation buttons.
--}}
