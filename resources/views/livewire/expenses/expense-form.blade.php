<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-surface border border-[#2A2A2A] rounded-2xl w-full max-w-md p-6 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-100">إضافة مصروف</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-100">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">التاريخ</label>
                    <input wire:model="expenseDate" type="date" class="w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                    @error('expenseDate') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">الفئة</label>
                    <select wire:model="categoryId" class="w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">اختر فئة</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('categoryId') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">المبلغ</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input wire:model="amount" type="number" step="0.01" class="pr-7 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    @error('amount') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">الوصف (اختياري)</label>
                    <textarea wire:model="description" rows="3" class="w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500"></textarea>
                    @error('description') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-[#2A2A2A]">
                    <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-xl text-gray-400 hover:text-gray-100 hover:bg-elevated transition-colors">
                        إلغاء
                    </button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-amber-500 text-black font-bold hover:bg-amber-400 transition-colors">
                        حفظ المصروف
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
