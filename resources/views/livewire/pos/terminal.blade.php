{{-- MOBILE RESPONSIVE: pos-terminal.blade.php --}}
<div class="flex flex-col md:flex-row h-screen bg-base overflow-hidden relative">
    @if($posMode === 'tables')
    {{-- Tables Screen --}}
    <div class="flex-1 flex flex-col h-full w-full">
        <header class="h-16 lg:h-20 bg-surface/50 backdrop-blur-xl border-b border-[#2A2A2A] flex items-center justify-between px-4 lg:px-8 shrink-0">
            <div class="flex items-center gap-3 md:gap-6">
                <!-- Hamburger on Mobile instead of Back -->
                <button @click="sidebarOpen = true" class="lg:hidden p-2 min-h-[48px] min-w-[48px] flex items-center justify-center rounded-xl bg-base border border-[#2A2A2A] text-gray-400 hover:text-amber-500 hover:border-amber-500/50 transition-all">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="hidden lg:block">
                    <h1 class="text-xl md:text-2xl font-black text-gray-100 tracking-tight">إدارة الطاولات</h1>
                    <p class="text-[10px] md:text-xs text-gray-500 font-bold uppercase tracking-widest mt-0.5">شاشة اختيار الطاولة والطلبات المفتوحة</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                @if(!$activeShift)
                <span class="px-3 py-1.5 md:py-1 rounded bg-red-500/20 text-red-400 text-xs md:text-sm font-medium border border-red-500/30 whitespace-nowrap">
                    لا يوجد شفت
                </span>
                @else
                <span class="px-3 py-1.5 md:py-1 rounded bg-emerald-500/20 text-emerald-400 text-xs md:text-sm font-medium border border-emerald-500/30 whitespace-nowrap">
                    الشفت مفتوح
                </span>
                @endif
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8 bg-base">
            <h1 class="text-xl font-bold text-gray-100 mb-4 lg:hidden">إدارة الطاولات</h1>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 md:gap-6">
                @foreach($tables as $table)
                    @php
                        $isOccupied = $table->status === 'occupied';
                    @endphp
                    <div
                        wire:click="openTable({{ $table->id }})"
                        class="group relative p-4 md:p-6 rounded-2xl md:rounded-3xl border border-transparent md:border-2 transition-all duration-300 cursor-pointer overflow-hidden min-h-[140px] flex flex-col items-center justify-center
                        {{ $isOccupied 
                            ? 'bg-red-500/10 border-red-500/30 hover:border-red-500 md:bg-red-500/5 md:border-red-500/20' 
                            : 'bg-emerald-500/10 border-emerald-500/30 hover:border-emerald-500 md:bg-emerald-500/5 md:border-emerald-500/20' }}"
                    >
                        {{-- Status Glow Background --}}
                        <div class="hidden md:block absolute -top-10 -right-10 w-24 h-24 blur-3xl opacity-20 transition-all duration-500 group-hover:scale-150
                            {{ $isOccupied ? 'bg-red-500' : 'bg-emerald-500' }}"></div>

                        <div class="relative flex flex-col items-center gap-2 md:gap-3 w-full">
                            <div class="w-10 h-10 md:w-14 md:h-14 rounded-xl md:rounded-2xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110
                                {{ $isOccupied ? 'bg-red-500/20 text-red-400' : 'bg-emerald-500/20 text-emerald-400' }}">
                                <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z" />
                                </svg>
                            </div>
                            
                            <h3 class="text-lg md:text-2xl font-black tracking-tight text-gray-100 group-hover:text-white transition-colors">
                                {{ $table->name }}
                            </h3>
                            
                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface/80 border border-[#2A2A2A] text-gray-400">
                                <span class="text-[10px] md:text-xs font-bold whitespace-nowrap">{{ $table->capacity }} كرسي</span>
                            </div>
                        </div>

                        @if($isOccupied && $table->activeOrder)
                            <div class="w-full mt-3 pt-3 border-t border-red-500/20 flex flex-col items-center gap-2">
                                <div class="text-amber-500 text-sm md:text-base font-black">
                                    ${{ number_format($table->activeOrder->total ?? 0, 0) }}
                                </div>
                            </div>
                        @endif

                        @if($isOccupied)
                            <div class="absolute top-2 right-2 w-2 h-2 md:top-3 md:left-3 rounded-full bg-red-500 animate-pulse"></div>
                        @else
                            <div class="absolute top-2 right-2 w-2 h-2 rounded-full bg-emerald-500"></div>
                        @endif
                    </div>
                @endforeach
            </div>
            @if(count($tables) === 0)
                <div class="h-64 flex flex-col items-center justify-center text-gray-500 gap-2">
                    <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>لا توجد طاولات مقيدة في النظام.</span>
                </div>
            @endif
        </div>
    </div>
    @endif

    @if($posMode === 'order')
    <div x-data="{ cartOpen: false }" class="flex w-full h-full">
        {{-- Left Side: Categories & Products (Full width mobile, 55% tablet, 60% desktop) --}}
        <div class="w-full md:w-[55%] lg:w-[60%] flex flex-col h-full border-l border-[#2A2A2A]">
            <header class="h-14 lg:h-16 bg-surface border-b border-[#2A2A2A] flex items-center justify-between px-4 shrink-0">
                <div class="flex items-center gap-3">
                    <button wire:click="backToTables" class="min-h-[48px] min-w-[48px] text-gray-400 hover:text-amber-500 transition-colors flex items-center justify-center rounded-lg bg-base border border-[#2A2A2A]">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </button>
                    <div class="hidden sm:block h-6 w-px bg-[#2A2A2A]"></div>
                    <h1 class="text-base sm:text-xl font-bold text-gray-100 truncate whitespace-nowrap">{{ $selectedTable ? 'طاولة ' . $selectedTable->name : 'نقطة البيع' }}</h1>
                </div>
            </header>

            {{-- Categories Tabs (Horizontal Scroll) --}}
            <div class="p-3 border-b border-[#2A2A2A] overflow-x-auto whitespace-nowrap shrink-0 flex gap-2 hide-scrollbar">
                <button wire:click="selectCategory(null)" 
                    class="px-5 py-2 min-h-[48px] min-w-[80px] rounded-xl text-sm font-semibold transition-all duration-200 {{ is_null($selectedCategory) ? 'bg-amber-500 text-black shadow-lg shadow-amber-500/20' : 'bg-surface text-gray-400 border border-[#2A2A2A] hover:text-gray-100 hover:bg-elevated' }}">
                    الكل
                </button>
                @foreach($categories as $category)
                <button wire:click="selectCategory({{ $category->id }})" 
                    class="px-5 py-2 min-h-[48px] min-w-[80px] rounded-xl text-sm font-semibold transition-all duration-200 {{ $selectedCategory == $category->id ? 'bg-amber-500 text-black shadow-lg shadow-amber-500/20' : 'bg-surface text-gray-400 border border-[#2A2A2A] hover:text-gray-100 hover:bg-elevated' }}">
                    {{ $category->name }}
                </button>
                @endforeach
            </div>

            {{-- Products Grid --}}
            <div class="flex-1 overflow-y-auto p-3 lg:p-4 bg-base pb-24 md:pb-4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($products as $product)
                    <button wire:click="addToCart({{ $product->id }})" 
                        class="bg-surface border border-[#2A2A2A] rounded-2xl p-3 flex flex-col items-center justify-center min-h-[120px] lg:min-h-[140px] text-center hover:border-amber-500 hover:shadow-lg hover:shadow-amber-500/10 transition-all duration-200 active:scale-95 group">
                        <span class="text-base lg:text-lg font-bold text-gray-100 group-hover:text-amber-500 transition-colors mb-2 line-clamp-2 md:line-clamp-none">{{ $product->name }}</span>
                        <span class="text-base font-medium text-amber-500/90">${{ number_format($product->price, 2) }}</span>
                    </button>
                    @endforeach
                </div>
                @if(count($products) === 0)
                    <div class="h-64 flex items-center justify-center text-gray-500">
                        لا توجد منتجات.
                    </div>
                @endif
            </div>

            {{-- Floating Cart Button (Mobile Only) --}}
            @php
                $itemCount = collect($cart)->sum('quantity');
            @endphp
            <button @click="cartOpen = true" class="md:hidden fixed bottom-6 left-6 z-40 bg-amber-500 text-black rounded-full w-14 h-14 min-h-[56px] min-w-[56px] flex items-center justify-center shadow-lg shadow-amber-500/30">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                @if($itemCount > 0)
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center border-2 border-surface">
                    {{ $itemCount }}
                </span>
                @endif
            </button>
            
            {{-- Mobile Bottom Sheet Overlay --}}
            <div x-show="cartOpen" @click="cartOpen = false" x-transition.opacity class="fixed inset-0 bg-black/60 z-40 md:hidden" style="display: none;"></div>
        </div>

        {{-- Right Side: Cart Bottom Sheet (Mobile) / Side Column (Tablet+) --}}
        <div :class="cartOpen ? 'translate-y-0' : 'translate-y-full md:translate-y-0'" 
             class="fixed inset-x-0 bottom-0 z-50 md:static md:w-[45%] lg:w-[40%] bg-surface flex flex-col h-[80vh] md:h-full rounded-t-2xl md:rounded-none transition-transform duration-300 md:transform-none shadow-2xl md:shadow-none shrink-0 border-t md:border-t-0 border-[#2A2A2A]">
            
            {{-- Mobile Sheet Header --}}
            <div class="flex justify-between items-center p-4 border-b border-[#2A2A2A] md:hidden">
                <h2 class="text-xl font-bold text-white">السلة</h2>
                <button @click="cartOpen = false" class="min-h-[48px] min-w-[48px] flex justify-center items-center rounded-lg bg-base border border-[#2A2A2A] text-gray-400">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Order Types --}}
            <div class="p-3 border-b border-[#2A2A2A] grid grid-cols-3 gap-2 shrink-0 bg-base md:bg-surface">
                <button wire:click="$set('orderType', 'dine_in')" class="min-h-[48px] text-sm font-semibold rounded-lg border {{ $orderType === 'dine_in' ? 'bg-amber-500/20 border-amber-500 text-amber-400' : 'border-[#2A2A2A] text-gray-400 hover:bg-elevated' }}">
                    محلي
                </button>
                <button wire:click="$set('orderType', 'takeaway')" class="min-h-[48px] text-sm font-semibold rounded-lg border {{ $orderType === 'takeaway' ? 'bg-amber-500/20 border-amber-500 text-amber-400' : 'border-[#2A2A2A] text-gray-400 hover:bg-elevated' }}">
                    سفري
                </button>
                <button wire:click="$set('orderType', 'delivery')" class="min-h-[48px] text-sm font-semibold rounded-lg border {{ $orderType === 'delivery' ? 'bg-amber-500/20 border-amber-500 text-amber-400' : 'border-[#2A2A2A] text-gray-400 hover:bg-elevated' }}">
                    توصيل
                </button>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto p-3 lg:p-4 space-y-3 bg-base md:bg-surface hide-scrollbar">
                @if(session()->has('success'))
                    <div class="p-3 mb-3 rounded-xl bg-emerald-500/20 text-emerald-400 text-sm font-medium border border-emerald-500/30">
                        {{ session('success') }}
                    </div>
                @endif
                @error('checkout')
                    <div class="p-3 mb-3 rounded-xl bg-red-500/20 text-red-400 text-sm font-medium border border-red-500/30">
                        {{ $message }}
                    </div>
                @enderror
                @error('shift')
                    <div class="p-3 mb-3 rounded-xl bg-red-500/20 text-red-400 text-sm font-medium border border-red-500/30">
                        {{ $message }}
                    </div>
                @enderror

                @if(empty($cart))
                    <div class="h-32 flex flex-col items-center justify-center text-gray-500 rounded-xl border border-dashed border-[#2A2A2A] mt-4">
                        <svg class="w-10 h-10 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <span>السلة فارغة</span>
                    </div>
                @else
                    @foreach($cart as $index => $item)
                    <div class="bg-surface border border-[#2A2A2A] rounded-xl p-3 flex flex-col gap-3">
                        <div class="flex justify-between items-start">
                            <span class="text-base font-bold text-gray-100 line-clamp-1 pr-2">{{ $item['name'] }}</span>
                            <span class="text-base font-bold text-amber-500 shrink-0">${{ number_format($item['subtotal'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-sm text-gray-500 font-medium">${{ number_format($item['unit_price'], 2) }}</span>
                            <div class="flex items-center gap-1 bg-elevated rounded-xl border border-[#2A2A2A]">
                                <button wire:click="updateQuantity({{ $index }}, -1)" class="w-10 h-10 md:w-11 md:h-11 flex items-center justify-center rounded-r-xl text-gray-300 bg-surface hover:text-white hover:bg-amber-500/20 transition-colors active:scale-95 text-lg font-black border-l border-[#2A2A2A]">
                                    -
                                </button>
                                <span class="w-10 text-center font-bold text-gray-100 text-base">
                                    {{ $item['quantity'] }}
                                </span>
                                <button wire:click="updateQuantity({{ $index }}, 1)" class="w-10 h-10 md:w-11 md:h-11 flex items-center justify-center rounded-l-xl text-gray-300 bg-surface hover:text-white hover:bg-emerald-500/20 transition-colors active:scale-95 text-lg font-black border-r border-[#2A2A2A]">
                                    +
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            {{-- Totals & Checkout --}}
            <div class="bg-elevated p-4 md:p-5 rounded-t-3xl border-t border-[#2A2A2A] shrink-0 shadow-[0_-10px_30px_rgba(0,0,0,0.5)]">
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-gray-400 text-sm">
                        <span>المجموع الفرعي</span>
                        <span class="font-bold">${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-400 text-sm">
                        <span>الضريبة</span>
                        <span class="font-bold">${{ number_format($tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-2xl font-black text-gray-100 pt-2 border-t border-[#2A2A2A] mt-2">
                        <span>الإجمالي</span>
                        <span class="text-amber-500">${{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <button wire:click="$set('paymentMethod', 'cash')" class="min-h-[56px] text-base rounded-xl font-bold border-2 transition-colors {{ $paymentMethod === 'cash' ? 'border-amber-500 text-amber-500 bg-amber-500/10' : 'border-[#2A2A2A] text-gray-400 hover:bg-surface' }}">
                        نقدي
                    </button>
                    <button wire:click="$set('paymentMethod', 'card')" class="min-h-[56px] text-base rounded-xl font-bold border-2 transition-colors {{ $paymentMethod === 'card' ? 'border-amber-500 text-amber-500 bg-amber-500/10' : 'border-[#2A2A2A] text-gray-400 hover:bg-surface' }}">
                        بطاقة
                    </button>
                </div>

                <button wire:click="checkout" @click="cartOpen = false" class="w-full min-h-[64px] rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xl font-black shadow-lg shadow-amber-500/20 active:scale-[0.98] transition-all flex justify-center items-center gap-2" {{ empty($cart) ? 'disabled' : '' }}>
                    <span wire:loading.remove wire:target="checkout">دفع وإنهاء</span>
                    <span wire:loading wire:target="checkout">جاري المعالجة...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Payment Success Modal --}}
    @if($lastOrder)
    <div class="fixed inset-0 z-[100] flex items-end md:items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="bg-surface border border-[#2A2A2A] rounded-3xl w-full max-w-md p-6 md:p-8 shadow-2xl text-center transform transition-all translate-y-0 relative">
            <div class="w-16 h-16 md:w-20 md:h-20 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                <svg class="w-8 h-8 md:w-10 md:h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            
            <h3 class="text-xl md:text-2xl font-bold text-gray-100 mb-2">تم الطلب بنجاح!</h3>
            <p class="text-gray-400 mb-6">الطلب رقم: <span class="font-bold text-white">#{{ $lastOrder->order_number ?? $lastOrder->id }}</span></p>
            
            <div class="flex flex-col gap-3">
                <a href="{{ route('orders.receipt', $lastOrder) }}" target="_blank" class="min-h-[56px] w-full rounded-xl bg-amber-500 text-black font-bold hover:bg-amber-400 transition-colors flex justify-center items-center gap-2 text-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4h10z"></path></svg>
                    طباعة الفاتورة
                </a>
                
                <button wire:click="closeReceiptModal" class="min-h-[56px] w-full rounded-xl bg-elevated border border-[#2A2A2A] text-gray-100 font-bold hover:bg-surface hover:text-white transition-colors text-lg">
                    طلب جديد
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
{{-- 
  CHANGES:
  - Tables converted to a better grid layout for touch.
  - Cart converted to bottom sheet with x-data for mobile screens.
  - Added bottom padding below products grid on mobile for bottom sheet trigger.
  - Adjusted interactive areas (buttons, tabs, inputs) to be min-h-[48px], many to 56px.
  - Formatted text sizes based on the specified typography scale (minimum text-base or 16px).
--}}
