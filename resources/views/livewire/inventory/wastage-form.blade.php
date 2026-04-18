<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-surface border border-[#2A2A2A] rounded-2xl w-full max-w-md p-6 shadow-2xl">
            <h3 class="text-xl font-bold text-gray-100 mb-6">تسجيل هالك</h3>
            
            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300">المكون</label>
                    <select wire:model="ingredient_id" class="mt-1 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">اختر مكوناً</option>
                        @foreach($availableIngredients as $ingredient)
                            <option value="{{ $ingredient->id }}">{{ $ingredient->name }} ({{ $ingredient->unit }})</option>
                        @endforeach
                    </select>
                    @error('ingredient_id') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300">الكمية المهدرة</label>
                    <input wire:model="qty_wasted" type="number" step="0.001" class="mt-1 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                    @error('qty_wasted') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300">السبب</label>
                    <select wire:model="reason" class="mt-1 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                        <option value="spillage">انسكاب</option>
                        <option value="expired">منتهي الصلاحية</option>
                        <option value="damaged">تالف</option>
                        <option value="other">أخرى</option>
                    </select>
                    @error('reason') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300">ملاحظات (اختياري)</label>
                    <textarea wire:model="notes" rows="3" class="mt-1 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500"></textarea>
                    @error('notes') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-xl text-gray-400 hover:text-gray-100 hover:bg-elevated transition-colors">
                        إلغاء
                    </button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-red-500 text-white font-bold hover:bg-red-600 transition-colors flex items-center">
                        <span wire:loading.remove>تسجيل الهالك</span>
                        <span wire:loading>جاري التسجيل...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
