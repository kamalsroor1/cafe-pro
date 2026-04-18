<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-surface border border-[#2A2A2A] rounded-2xl w-full max-w-lg p-6 shadow-2xl">
            <h3 class="text-xl font-bold text-gray-100 mb-6">{{ $product_id ? 'تعديل منتج' : 'منتج جديد' }}</h3>
            
            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300">الاسم</label>
                    <input wire:model="name" type="text" class="mt-1 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                    @error('name') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300">الفئة</label>
                    <select wire:model="category_id" class="mt-1 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">اختر فئة</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300">السعر</label>
                        <input wire:model="price" type="number" step="0.01" class="mt-1 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                        @error('price') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">التكلفة (اختياري)</label>
                        <input wire:model="cost" type="number" step="0.01" class="mt-1 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                        @error('cost') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex items-center mt-4 gap-2">
                    <input wire:model="is_available" type="checkbox" id="is_available" class="w-4 h-4 rounded bg-base border-[#2A2A2A] text-amber-500 focus:ring-amber-500 focus:ring-offset-surface">
                    <label for="is_available" class="text-sm font-medium text-gray-300">متاح للبيع</label>
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
