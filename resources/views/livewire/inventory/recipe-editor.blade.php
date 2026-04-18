<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-surface border border-[#2A2A2A] rounded-2xl w-full max-w-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-100">Recipe: {{ $productName }}</h3>
                <button wire:click="$set('isOpen', false)" class="text-gray-400 hover:text-gray-100">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="space-y-4">
                @foreach($recipe as $index => $item)
                <div class="flex items-center gap-4 bg-elevated p-4 rounded-xl border border-[#2A2A2A]">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-400 mb-1">Ingredient</label>
                        <select wire:model="recipe.{{ $index }}.ingredient_id" class="w-full bg-base border border-[#2A2A2A] rounded-lg px-3 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                            <option value="">Select...</option>
                            @foreach($availableIngredients as $ingredient)
                                <option value="{{ $ingredient->id }}">{{ $ingredient->name }} ({{ $ingredient->unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="w-32">
                        <label class="block text-xs font-medium text-gray-400 mb-1">Amount</label>
                        <input wire:model="recipe.{{ $index }}.amount" type="number" step="0.001" class="w-full bg-base border border-[#2A2A2A] rounded-lg px-3 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    
                    <div class="pt-5">
                        <button wire:click="removeIngredient({{ $index }})" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
                @endforeach
                
                @if(count($recipe) === 0)
                <div class="text-center py-8 text-gray-400 border border-dashed border-[#2A2A2A] rounded-xl">
                    No ingredients added yet.
                </div>
                @endif
                
                <button wire:click="addIngredient" class="w-full py-3 border border-dashed border-[#2A2A2A] rounded-xl text-amber-500 font-medium hover:bg-elevated transition-colors">
                    + Add Ingredient
                </button>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-[#2A2A2A]">
                <button wire:click="$set('isOpen', false)" class="px-4 py-2 rounded-xl text-gray-400 hover:text-gray-100 hover:bg-elevated transition-colors">
                    Cancel
                </button>
                <button wire:click="save" class="px-6 py-2 rounded-xl bg-amber-500 text-black font-bold hover:bg-amber-400 transition-colors flex items-center">
                    <span wire:loading.remove>Save Recipe</span>
                    <span wire:loading>Saving...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
