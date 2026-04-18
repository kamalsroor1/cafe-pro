<div class="flex h-screen bg-base overflow-hidden">
    {{-- Left Side: Categories & Products --}}
    <div class="flex-1 flex flex-col h-full border-r border-[#2A2A2A]">
        {{-- Header --}}
        <header class="h-16 bg-surface border-b border-[#2A2A2A] flex items-center justify-between px-6 shrink-0">
            <div class="flex items-center gap-4">
                <a href="/" class="text-gray-400 hover:text-amber-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-xl font-bold text-gray-100">POS Terminal</h1>
            </div>
            
            <div class="flex gap-2">
                @if(!$activeShift)
                <span class="px-3 py-1 rounded bg-red-500/20 text-red-400 text-sm font-medium border border-red-500/30">
                    No Active Shift
                </span>
                @else
                <span class="px-3 py-1 rounded bg-emerald-500/20 text-emerald-400 text-sm font-medium border border-emerald-500/30">
                    Shift Open
                </span>
                @endif
            </div>
        </header>

        {{-- Categories --}}
        <div class="p-4 border-b border-[#2A2A2A] overflow-x-auto whitespace-nowrap shrink-0 hide-scrollbar">
            <button wire:click="selectCategory(null)" 
                class="px-6 py-3 min-h-[48px] rounded-xl font-semibold mr-2 transition-all duration-200 {{ is_null($selectedCategory) ? 'bg-amber-500 text-black shadow-lg shadow-amber-500/20' : 'bg-surface text-gray-400 border border-[#2A2A2A] hover:text-gray-100 hover:border-gray-500' }}">
                All
            </button>
            @foreach($categories as $category)
            <button wire:click="selectCategory({{ $category->id }})" 
                class="px-6 py-3 min-h-[48px] rounded-xl font-semibold mr-2 transition-all duration-200 {{ $selectedCategory == $category->id ? 'bg-amber-500 text-black shadow-lg shadow-amber-500/20' : 'bg-surface text-gray-400 border border-[#2A2A2A] hover:text-gray-100 hover:border-gray-500' }}">
                {{ $category->name }}
            </button>
            @endforeach
        </div>

        {{-- Products Grid --}}
        <div class="flex-1 overflow-y-auto p-6 bg-base">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @foreach($products as $product)
                <button wire:click="addToCart({{ $product->id }})" 
                    class="bg-surface border border-[#2A2A2A] rounded-2xl p-4 flex flex-col items-center justify-center min-h-[140px] text-center hover:border-amber-500 hover:shadow-lg hover:shadow-amber-500/10 transition-all duration-200 active:scale-95 group">
                    <span class="text-lg font-bold text-gray-100 group-hover:text-amber-500 transition-colors mb-2">{{ $product->name }}</span>
                    <span class="text-sm font-medium text-amber-500/80">${{ number_format($product->price, 2) }}</span>
                </button>
                @endforeach
            </div>
            @if(count($products) === 0)
                <div class="h-full flex items-center justify-center text-gray-500">
                    No products found in this category.
                </div>
            @endif
        </div>
    </div>

    {{-- Right Side: Cart --}}
    <div class="w-96 bg-surface flex flex-col h-full border-l border-[#2A2A2A] shrink-0">
        {{-- Order Types --}}
        <div class="p-4 border-b border-[#2A2A2A] grid grid-cols-3 gap-2 shrink-0">
            <button wire:click="$set('orderType', 'dine_in')" class="py-2 px-1 text-sm font-semibold rounded-lg border {{ $orderType === 'dine_in' ? 'bg-amber-500/20 border-amber-500 text-amber-400' : 'border-[#2A2A2A] text-gray-400 hover:text-gray-200' }}">
                Dine In
            </button>
            <button wire:click="$set('orderType', 'takeaway')" class="py-2 px-1 text-sm font-semibold rounded-lg border {{ $orderType === 'takeaway' ? 'bg-amber-500/20 border-amber-500 text-amber-400' : 'border-[#2A2A2A] text-gray-400 hover:text-gray-200' }}">
                Takeaway
            </button>
            <button wire:click="$set('orderType', 'delivery')" class="py-2 px-1 text-sm font-semibold rounded-lg border {{ $orderType === 'delivery' ? 'bg-amber-500/20 border-amber-500 text-amber-400' : 'border-[#2A2A2A] text-gray-400 hover:text-gray-200' }}">
                Delivery
            </button>
        </div>

        {{-- Cart Items --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            @if(session()->has('success'))
                <div class="p-3 mb-4 rounded-xl bg-emerald-500/20 text-emerald-400 text-sm font-medium text-center border border-emerald-500/30">
                    {{ session('success') }}
                </div>
            @endif

            @error('checkout')
                <div class="p-3 mb-4 rounded-xl bg-red-500/20 text-red-400 text-sm font-medium text-center border border-red-500/30">
                    {{ $message }}
                </div>
            @enderror
            
            @error('shift')
                <div class="p-3 mb-4 rounded-xl bg-red-500/20 text-red-400 text-sm font-medium text-center border border-red-500/30">
                    {{ $message }}
                </div>
            @enderror

            @if(empty($cart))
                <div class="h-full flex items-center justify-center text-gray-500 flex-col gap-2">
                    <svg class="w-12 h-12 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span>Cart is empty</span>
                </div>
            @else
                @foreach($cart as $index => $item)
                <div class="bg-base border border-[#2A2A2A] rounded-xl p-3 flex flex-col gap-3">
                    <div class="flex justify-between items-start">
                        <span class="font-bold text-gray-100">{{ $item['name'] }}</span>
                        <span class="font-bold text-amber-500">${{ number_format($item['subtotal'], 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">${{ number_format($item['unit_price'], 2) }}</span>
                        <div class="flex items-center gap-3 bg-surface rounded-lg border border-[#2A2A2A] p-1">
                            <button wire:click="updateQuantity({{ $index }}, -1)" class="w-8 h-8 flex items-center justify-center rounded text-gray-400 hover:text-gray-100 hover:bg-elevated transition-colors active:scale-95">
                                -
                            </button>
                            <span class="w-6 text-center font-bold text-gray-100">{{ $item['quantity'] }}</span>
                            <button wire:click="updateQuantity({{ $index }}, 1)" class="w-8 h-8 flex items-center justify-center rounded text-gray-400 hover:text-gray-100 hover:bg-elevated transition-colors active:scale-95">
                                +
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        {{-- Totals & Checkout --}}
        <div class="bg-elevated p-6 rounded-t-3xl border-t border-[#2A2A2A] shrink-0 shadow-[0_-10px_40px_rgba(0,0,0,0.3)]">
            <div class="space-y-3 mb-6">
                <div class="flex justify-between text-gray-400">
                    <span>Subtotal</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-400">
                    <span>Tax</span>
                    <span>${{ number_format($tax, 2) }}</span>
                </div>
                <div class="flex justify-between text-2xl font-bold text-gray-100 pt-3 border-t border-[#2A2A2A]">
                    <span>Total</span>
                    <span class="text-amber-500">${{ number_format($total, 2) }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <button wire:click="$set('paymentMethod', 'cash')" class="py-3 rounded-xl font-bold border-2 transition-colors {{ $paymentMethod === 'cash' ? 'border-amber-500 text-amber-500 bg-amber-500/10' : 'border-[#2A2A2A] text-gray-400 hover:border-gray-500' }}">
                    CASH
                </button>
                <button wire:click="$set('paymentMethod', 'card')" class="py-3 rounded-xl font-bold border-2 transition-colors {{ $paymentMethod === 'card' ? 'border-amber-500 text-amber-500 bg-amber-500/10' : 'border-[#2A2A2A] text-gray-400 hover:border-gray-500' }}">
                    CARD
                </button>
            </div>

            <button wire:click="checkout" class="w-full py-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xl font-bold shadow-lg shadow-amber-500/20 active:scale-[0.98] transition-all flex justify-center items-center gap-2">
                <span wire:loading.remove wire:target="checkout">Pay & Complete</span>
                <span wire:loading wire:target="checkout">Processing...</span>
            </button>
        </div>
    </div>
</div>
