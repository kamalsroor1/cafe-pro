<div>
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-100">Products</h2>
        
        <div class="flex gap-4">
            <input wire:model.live="search" type="text" placeholder="Search products..." 
                class="bg-surface border border-[#2A2A2A] rounded-lg px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
            
            <button onclick="Livewire.dispatchTo('products.product-form', 'openModal')" class="bg-amber-500 text-black px-4 py-2 rounded-lg font-semibold hover:bg-amber-400 transition-colors">
                + New Product
            </button>
        </div>
    </div>

    <div class="bg-surface rounded-xl border border-[#2A2A2A] overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-elevated border-b border-[#2A2A2A]">
                <tr>
                    <th class="p-4 text-sm font-semibold text-gray-400">Name</th>
                    <th class="p-4 text-sm font-semibold text-gray-400">Category</th>
                    <th class="p-4 text-sm font-semibold text-gray-400">Price</th>
                    <th class="p-4 text-sm font-semibold text-gray-400">Status</th>
                    <th class="p-4 text-sm font-semibold text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#2A2A2A]">
                @foreach($products as $product)
                <tr class="hover:bg-elevated transition-colors">
                    <td class="p-4 text-gray-100 font-medium">{{ $product->name }}</td>
                    <td class="p-4 text-gray-400">{{ $product->category->name }}</td>
                    <td class="p-4 text-amber-500 font-bold">{{ number_format($product->price, 2) }}</td>
                    <td class="p-4">
                        @if($product->is_available)
                        <span class="px-2 py-1 rounded bg-emerald-500/20 text-emerald-400 text-xs font-medium">Available</span>
                        @else
                        <span class="px-2 py-1 rounded bg-red-500/20 text-red-400 text-xs font-medium">Unavailable</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <div class="flex gap-2 items-center">
                            {{-- Edit Recipe --}}
                            <button
                                onclick="Livewire.dispatchTo('inventory.recipe-editor', 'openRecipeModal', { productId: {{ $product->id }} })"
                                class="p-2 min-h-[40px] min-w-[40px] flex items-center justify-center text-gray-400 hover:text-emerald-400 hover:bg-emerald-500/10 rounded-lg transition-colors"
                                title="Edit Recipe">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                            </button>

                            
                            {{-- Edit Product --}}
                            <button
                                onclick="Livewire.dispatchTo('products.product-form', 'openModal', { product_id: {{ $product->id }} })"
                                class="p-2 min-h-[40px] min-w-[40px] flex items-center justify-center text-gray-400 hover:text-amber-500 hover:bg-amber-500/10 rounded-lg transition-colors"
                                title="Edit Product">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>

                            {{-- Delete --}}
                            <button
                                wire:click="delete({{ $product->id }})"
                                class="p-2 min-h-[40px] min-w-[40px] flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-colors"
                                title="Delete">
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
        
        <div class="p-4 border-t border-[#2A2A2A]">
            {{ $products->links() }}
        </div>
    </div>

    @livewire('products.product-form')
    @livewire('inventory.recipe-editor')
</div>
