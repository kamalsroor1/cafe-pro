{{-- MOBILE RESPONSIVE: recipe-editor.blade.php --}}
<div>
    @if($isOpen)
    <div class="fixed inset-0 z-[100] flex items-end md:items-center justify-center bg-black/60 backdrop-blur-sm p-0 md:p-4 transition-all">
        <div class="bg-surface border-t md:border border-[#2A2A2A] rounded-t-3xl md:rounded-3xl w-full max-w-2xl p-6 shadow-2xl overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center mb-6 border-b border-[#2A2A2A] pb-4">
                <h3 class="text-xl md:text-2xl font-bold text-gray-100">وصفة: {{ $productName }}</h3>
                <button wire:click="$set('isOpen', false)" class="p-2 -mr-2 bg-base rounded-lg text-gray-400 hover:text-gray-100 hover:bg-elevated transition-colors active:scale-95">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="space-y-4">
                @foreach($recipe as $index => $item)
                <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3 bg-elevated p-4 rounded-xl border border-[#2A2A2A]">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-400 mb-1">المكون</label>
                        <select wire:model="recipe.{{ $index }}.ingredient_id" class="min-h-[56px] md:min-h-[48px] w-full bg-base border border-[#2A2A2A] rounded-lg px-3 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500 appearance-none">
                            <option value="">اختر...</option>
                            @foreach($availableIngredients as $ingredient)
                                <option value="{{ $ingredient->id }}">{{ $ingredient->name }} ({{ $ingredient->unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex items-end gap-3 flex-row w-full md:w-auto">
                        <div class="flex-1 md:w-32">
                            <label class="block text-sm font-medium text-gray-400 mb-1">الكمية</label>
                            <input wire:model="recipe.{{ $index }}.amount" type="number" step="0.001" inputmode="decimal" class="min-h-[56px] md:min-h-[48px] w-full bg-base border border-[#2A2A2A] rounded-lg px-3 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        
                        <div class="pt-0 shrink-0">
                            <button wire:click="removeIngredient({{ $index }})" class="min-h-[56px] md:min-h-[48px] px-4 md:px-2 min-w-[56px] md:min-w-[48px] flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-colors border border-transparent hover:border-red-500/20 bg-base active:scale-95">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
                
                @if(count($recipe) === 0)
                <div class="text-center py-10 md:py-8 text-gray-400 border border-dashed border-[#2A2A2A] rounded-xl font-medium bg-base/50">
                    لم يتم إضافة أي مكونات بعد.
                </div>
                @endif
                
                <button wire:click="addIngredient" class="min-h-[56px] w-full border border-dashed border-[#2A2A2A] rounded-xl text-amber-500 font-bold hover:bg-elevated transition-colors bg-amber-500/5 hover:border-amber-500/30 active:scale-[0.98]">
                    + إضافة مكون للوصفة
                </button>
            </div>

            <div class="flex flex-col-reverse md:flex-row justify-end gap-3 md:gap-4 mt-8 pt-4 border-t border-[#2A2A2A]">
                <button wire:click="$set('isOpen', false)" class="min-h-[56px] w-full md:w-auto px-6 rounded-xl text-gray-400 hover:text-gray-100 bg-elevated border border-[#2A2A2A] hover:bg-surface transition-colors font-bold text-lg">
                    إلغاء الأمر
                </button>
                <button wire:click="save" class="min-h-[56px] w-full md:w-auto px-8 rounded-xl bg-amber-500 text-black font-black hover:bg-amber-400 shadow-lg shadow-amber-500/20 active:scale-95 transition-all flex items-center justify-center gap-2 text-lg">
                    <span wire:loading.remove>حفظ الوصفة</span>
                    <span wire:loading>جاري الحفظ...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
{{-- 
  CHANGES:
  - Repositioned ingredient items dynamically from horizontal to flex-col strictly on mobile. 
  - Bumped input dimensions safely for touch functionality (`min-h-[56px]`).
  - Drawer modal sizing implementation (`items-end rounded-t-3xl`).
--}}
