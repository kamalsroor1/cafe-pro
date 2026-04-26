{{-- MOBILE RESPONSIVE: product-list.blade.php --}}
<div>
    <div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h2 class="text-xl md:text-2xl font-bold text-gray-100">المنتجات</h2>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <input wire:model.live="search" type="text" placeholder="بحث في المنتجات..." 
                class="bg-surface border border-[#2A2A2A] rounded-xl px-4 py-2 min-h-[48px] text-gray-100 focus:border-amber-500 focus:ring-amber-500 w-full sm:w-64">
            
            <button onclick="Livewire.dispatchTo('products.product-form', 'openModal')" class="bg-amber-500 text-black px-4 py-2 min-h-[48px] rounded-xl font-bold hover:bg-amber-400 transition-colors w-full sm:w-auto active:scale-95 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                منتج جديد
            </button>
        </div>
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden md:block bg-surface rounded-2xl border border-[#2A2A2A] overflow-x-auto">
        <table class="w-full text-right whitespace-nowrap">
            <thead class="bg-elevated border-b border-[#2A2A2A]">
                <tr>
                    <th class="p-4 text-sm font-semibold text-gray-400">الاسم</th>
                    <th class="p-4 text-sm font-semibold text-gray-400">الفئة</th>
                    <th class="p-4 text-sm font-semibold text-gray-400">السعر</th>
                    <th class="p-4 text-sm font-semibold text-gray-400">الحالة</th>
                    <th class="p-4 text-sm font-semibold text-gray-400">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#2A2A2A]">
                @foreach($products as $product)
                <tr class="hover:bg-elevated transition-colors">
                    <td class="p-4 text-gray-100 font-medium">{{ $product->name }}</td>
                    <td class="p-4 text-gray-400">{{ $product->category->name }}</td>
                    <td class="p-4 text-amber-500 font-bold">${{ number_format($product->price, 2) }}</td>
                    <td class="p-4">
                        @if($product->is_available)
                        <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold border border-emerald-500/30">متاح</span>
                        @else
                        <span class="px-3 py-1 rounded-full bg-red-500/20 text-red-400 text-xs font-bold border border-red-500/30">غير متاح</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <div class="flex gap-2 items-center">
                            <button
                                onclick="Livewire.dispatchTo('inventory.recipe-editor', 'openRecipeModal', { productId: {{ $product->id }} })"
                                class="p-2 min-h-[40px] min-w-[40px] flex items-center justify-center text-gray-400 hover:text-emerald-400 hover:bg-emerald-500/10 rounded-lg transition-colors border border-transparent hover:border-emerald-500/30"
                                title="تعديل الوصفة">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                            </button>

                            <button
                                onclick="Livewire.dispatchTo('products.product-form', 'openModal', { product_id: {{ $product->id }} })"
                                class="p-2 min-h-[40px] min-w-[40px] flex items-center justify-center text-gray-400 hover:text-amber-500 hover:bg-amber-500/10 rounded-lg transition-colors border border-transparent hover:border-amber-500/30"
                                title="تعديل المنتج">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>

                            <button
                                wire:click="delete({{ $product->id }})"
                                class="p-2 min-h-[40px] min-w-[40px] flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-colors border border-transparent hover:border-red-500/30"
                                title="حذف">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards View --}}
    <div class="md:hidden space-y-3">
        @foreach($products as $product)
        <div class="bg-surface rounded-2xl p-4 border border-[#2A2A2A]">
            <div class="flex justify-between items-start mb-3">
                <div class="flex-1">
                    <p class="text-base font-bold text-gray-100 line-clamp-1">{{ $product->name }}</p>
                    <p class="text-sm text-gray-400 mt-0.5">{{ $product->category->name }}</p>
                </div>
                <div class="flex flex-col items-end gap-1">
                    <span class="text-amber-500 font-bold">${{ number_format($product->price, 2) }}</span>
                    @if($product->is_available)
                        <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 text-[10px] font-bold border border-emerald-500/30">متاح</span>
                    @else
                        <span class="px-2 py-0.5 rounded bg-red-500/20 text-red-400 text-[10px] font-bold border border-red-500/30">غير متاح</span>
                    @endif
                </div>
            </div>
            
            <div class="flex gap-2 pt-3 border-t border-[#2A2A2A]">
                <button
                    onclick="Livewire.dispatchTo('inventory.recipe-editor', 'openRecipeModal', { productId: {{ $product->id }} })"
                    class="flex-1 min-h-[48px] flex items-center justify-center gap-1.5 text-sm font-medium text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 rounded-xl transition-colors border border-emerald-500/20 active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    الوصفة
                </button>
                <button
                    onclick="Livewire.dispatchTo('products.product-form', 'openModal', { product_id: {{ $product->id }} })"
                    class="flex-1 min-h-[48px] flex items-center justify-center gap-1.5 text-sm font-medium text-amber-500 bg-amber-500/10 hover:bg-amber-500/20 rounded-xl transition-colors border border-amber-500/20 active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    تعديل
                </button>
                <button
                    wire:click="delete({{ $product->id }})"
                    class="flex-1 min-h-[48px] flex items-center justify-center gap-1.5 text-sm font-medium text-red-500 bg-red-500/10 hover:bg-red-500/20 rounded-xl transition-colors border border-red-500/20 active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    حذف
                </button>
            </div>
        </div>
        @endforeach
        
        @if(count($products) === 0)
        <div class="bg-surface rounded-2xl p-8 border border-[#2A2A2A] text-center text-gray-500">
            لا توجد منتجات.
        </div>
        @endif
    </div>

    <div class="mt-4 md:mt-0 md:p-4 md:border-t md:border-[#2A2A2A] md:bg-surface md:rounded-b-2xl">
        {{ $products->links() }}
    </div>

    @livewire('products.product-form')
    @livewire('inventory.recipe-editor')
</div>
{{-- 
  CHANGES:
  - Responsive header: stacks full width search input and buttons.
  - Table converted to hidden md:block desktop view.
  - Added Mobile Cards view (md:hidden) iterating the product items.
  - Formatted buttons as touch minimums (min-h-[48px]) inside cards.
  - Paginator adjustments for different breakpoints.
--}}
