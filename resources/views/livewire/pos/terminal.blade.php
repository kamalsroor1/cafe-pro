<div class="flex flex-col md:flex-row h-screen bg-base overflow-hidden">
    @if($posMode === 'tables')
    {{-- Tables Screen --}}
    <div class="flex-1 flex flex-col h-full">
        <header class="h-20 bg-surface/50 backdrop-blur-xl border-b border-[#2A2A2A] flex items-center justify-between px-8 shrink-0">
            <div class="flex items-center gap-6">
                <a href="/" class="p-2 rounded-xl bg-base border border-[#2A2A2A] text-gray-400 hover:text-amber-500 hover:border-amber-500/50 transition-all active:scale-95">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-black text-gray-100 tracking-tight">إدارة الطاولات</h1>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-0.5">شاشة اختيار الطاولة والطلبات المفتوحة</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                @if(!$activeShift)
                <span class="px-3 py-1 rounded bg-red-500/20 text-red-400 text-sm font-medium border border-red-500/30">
                    لا يوجد شفت مفتوح
                </span>
                @else
                <span class="px-3 py-1 rounded bg-emerald-500/20 text-emerald-400 text-sm font-medium border border-emerald-500/30">
                    الشفت مفتوح
                </span>
                @endif
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 md:p-8 bg-base">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
                @foreach($tables as $table)
                    @php
                        $isOccupied = $table->status === 'occupied';
                    @endphp
                    <div
                        wire:click="openTable({{ $table->id }})"
                        class="group relative p-6 rounded-3xl border-2 transition-all duration-300 cursor-pointer overflow-hidden
                        {{ $isOccupied 
                            ? 'bg-red-500/5 border-red-500/20 hover:border-red-500 hover:shadow-[0_0_20px_rgba(239,68,68,0.15)] shadow-red-500/5' 
                            : 'bg-emerald-500/5 border-emerald-500/20 hover:border-emerald-500 hover:shadow-[0_0_20px_rgba(16,185,129,0.15)] shadow-emerald-500/5' }}"
                    >
                        {{-- Status Glow Background --}}
                        <div class="absolute -top-10 -right-10 w-24 h-24 blur-3xl opacity-20 transition-all duration-500 group-hover:scale-150
                            {{ $isOccupied ? 'bg-red-500' : 'bg-emerald-500' }}"></div>

                        {{-- Table Icon & Name --}}
                        <div class="relative flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110
                                {{ $isOccupied ? 'bg-red-500/10 text-red-400' : 'bg-emerald-500/10 text-emerald-400' }}">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z" />
                                </svg>
                            </div>
                            
                            <h3 class="text-2xl font-black tracking-tight text-gray-100 group-hover:text-white transition-colors">
                                {{ $table->name }}
                            </h3>
                            
                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface/50 border border-[#2A2A2A] text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span class="text-xs font-bold uppercase tracking-wider">{{ $table->capacity }} أشخاص</span>
                            </div>
                        </div>

                        @if($isOccupied && $table->activeOrder)
                            {{-- Occupied State Info --}}
                            <div
                                class="mt-5 pt-4 border-t border-red-500/10 flex flex-col gap-3"
                                x-data="{
                                    start: new Date('{{ $table->activeOrder->created_at->toIso8601String() }}'),
                                    elapsed: '',
                                    tick() {
                                        const diff = Math.floor((new Date() - this.start) / 60000);
                                        this.elapsed = diff < 60
                                            ? diff + ' د'
                                            : Math.floor(diff / 60) + ' س ' + (diff % 60) + ' د';
                                    }
                                }"
                                x-init="tick(); setInterval(() => tick(), 60000)"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1 text-red-400">
                                        <svg class="w-3 h-3 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span x-text="elapsed" class="text-xs font-black"></span>
                                    </div>
                                    <div class="flex items-center gap-1 text-amber-500">
                                        <span class="text-xs font-black">${{ number_format($table->activeOrder->total ?? 0, 0) }}</span>
                                    </div>
                                </div>
                                
                                <div class="w-full bg-red-500/20 rounded-full h-1 overflow-hidden">
                                    <div class="bg-red-500 h-full animate-[progress_2s_ease-in-out_infinite]" style="width: 30%"></div>
                                </div>
                            </div>
                        @else
                            {{-- Available Badge --}}
                            <div class="mt-5 py-2 px-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-[0.2em] text-center opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0">
                                مـتـاحـة للـحـجـز
                            </div>
                        @endif

                        {{-- Active Indicator Ring (Occupied only) --}}
                        @if($isOccupied)
                            <div class="absolute top-3 left-3 w-2 h-2 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.8)] animate-pulse"></div>
                        @endif
                    </div>
                @endforeach
            </div>
            @if(count($tables) === 0)
                <div class="h-64 flex flex-col items-center justify-center text-gray-500 gap-2">
                    <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>لا توجد طاولات مسجلة في النظام.</span>
                </div>
            @endif
        </div>
    </div>
    @endif

    @if($posMode === 'order')
    {{-- Left Side: Categories & Products --}}
    <div class="flex-1 flex flex-col h-1/2 md:h-full border-b md:border-b-0 md:border-l border-[#2A2A2A]">
        <header class="h-16 bg-surface border-b border-[#2A2A2A] flex items-center justify-between px-6 shrink-0">
            <div class="flex items-center gap-4">
                <button wire:click="backToTables" class="text-gray-400 hover:text-amber-500 transition-colors flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span class="hidden sm:inline font-medium">رجوع للطاولات</span>
                </button>
                <div class="h-6 w-px bg-[#2A2A2A]"></div>
                <h1 class="text-xl font-bold text-gray-100">{{ $selectedTable ? 'طاولة: ' . $selectedTable->name : 'نقطة البيع' }}</h1>
            </div>
            
            <div class="flex gap-2">
                @if(!$activeShift)
                <span class="px-3 py-1 rounded bg-red-500/20 text-red-400 text-sm font-medium border border-red-500/30">
                    لا يوجد شفت مفتوح
                </span>
                @else
                <span class="px-3 py-1 rounded bg-emerald-500/20 text-emerald-400 text-sm font-medium border border-emerald-500/30">
                    الشفت مفتوح
                </span>
                @endif
            </div>
        </header>

        {{-- Categories --}}
        <div class="p-4 border-b border-[#2A2A2A] overflow-x-auto whitespace-nowrap shrink-0 hide-scrollbar">
            <button wire:click="selectCategory(null)" 
                class="px-6 py-3 min-h-[48px] rounded-xl font-semibold ml-2 transition-all duration-200 {{ is_null($selectedCategory) ? 'bg-amber-500 text-black shadow-lg shadow-amber-500/20' : 'bg-surface text-gray-400 border border-[#2A2A2A] hover:text-gray-100 hover:border-gray-500' }}">
                الكل
            </button>
            @foreach($categories as $category)
            <button wire:click="selectCategory({{ $category->id }})" 
                class="px-6 py-3 min-h-[48px] rounded-xl font-semibold ml-2 transition-all duration-200 {{ $selectedCategory == $category->id ? 'bg-amber-500 text-black shadow-lg shadow-amber-500/20' : 'bg-surface text-gray-400 border border-[#2A2A2A] hover:text-gray-100 hover:border-gray-500' }}">
                {{ $category->name }}
            </button>
            @endforeach
        </div>

        {{-- Products Grid --}}
        <div class="flex-1 overflow-y-auto p-4 md:p-6 bg-base">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-4">
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
                    لا توجد منتجات في هذا التصنيف.
                </div>
            @endif
        </div>
    </div>

    {{-- Right Side: Cart --}}
    <div class="w-full md:w-96 bg-surface flex flex-col h-1/2 md:h-full shrink-0">
        {{-- Order Types --}}
        <div class="p-4 border-b border-[#2A2A2A] grid grid-cols-3 gap-2 shrink-0">
            <button wire:click="$set('orderType', 'dine_in')" class="py-2 px-1 text-sm font-semibold rounded-lg border {{ $orderType === 'dine_in' ? 'bg-amber-500/20 border-amber-500 text-amber-400' : 'border-[#2A2A2A] text-gray-400 hover:text-gray-200' }}">
                محلي
            </button>
            <button wire:click="$set('orderType', 'takeaway')" class="py-2 px-1 text-sm font-semibold rounded-lg border {{ $orderType === 'takeaway' ? 'bg-amber-500/20 border-amber-500 text-amber-400' : 'border-[#2A2A2A] text-gray-400 hover:text-gray-200' }}">
                سفري
            </button>
            <button wire:click="$set('orderType', 'delivery')" class="py-2 px-1 text-sm font-semibold rounded-lg border {{ $orderType === 'delivery' ? 'bg-amber-500/20 border-amber-500 text-amber-400' : 'border-[#2A2A2A] text-gray-400 hover:text-gray-200' }}">
                توصيل
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
                    <span>السلة فارغة</span>
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
                    <span>المجموع الفرعي</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-400">
                    <span>الضريبة</span>
                    <span>${{ number_format($tax, 2) }}</span>
                </div>
                <div class="flex justify-between text-2xl font-bold text-gray-100 pt-3 border-t border-[#2A2A2A]">
                    <span>الإجمالي</span>
                    <span class="text-amber-500">${{ number_format($total, 2) }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <button wire:click="$set('paymentMethod', 'cash')" class="py-3 rounded-xl font-bold border-2 transition-colors {{ $paymentMethod === 'cash' ? 'border-amber-500 text-amber-500 bg-amber-500/10' : 'border-[#2A2A2A] text-gray-400 hover:border-gray-500' }}">
                    نقدي
                </button>
                <button wire:click="$set('paymentMethod', 'card')" class="py-3 rounded-xl font-bold border-2 transition-colors {{ $paymentMethod === 'card' ? 'border-amber-500 text-amber-500 bg-amber-500/10' : 'border-[#2A2A2A] text-gray-400 hover:border-gray-500' }}">
                    بطاقة
                </button>
            </div>

            <button wire:click="checkout" class="w-full py-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xl font-bold shadow-lg shadow-amber-500/20 active:scale-[0.98] transition-all flex justify-center items-center gap-2">
                <span wire:loading.remove wire:target="checkout">دفع وإنهاء</span>
                <span wire:loading wire:target="checkout">جاري المعالجة...</span>
            </button>
        </div>
    </div>
    @endif

    {{-- Payment Success & Receipt Modal --}}
    @if($lastOrder)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-surface border border-[#2A2A2A] rounded-2xl w-full max-w-md p-8 shadow-2xl text-center">
            <div class="w-20 h-20 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            
            <h3 class="text-2xl font-bold text-gray-100 mb-2">تم الدفع بنجاح!</h3>
            <p class="text-gray-400 mb-6">الطلب رقم: #{{ $lastOrder->order_number ?? $lastOrder->id }}</p>
            
            <div class="flex flex-col gap-3">
                <a href="{{ route('orders.receipt', $lastOrder) }}" target="_blank" class="w-full py-3 rounded-xl bg-amber-500 text-black font-bold hover:bg-amber-400 transition-colors flex justify-center items-center gap-2 active:scale-95 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4h10z"></path></svg>
                    طباعة الفاتورة
                </a>
                
                <button wire:click="closeReceiptModal" class="w-full py-3 rounded-xl bg-elevated border border-[#2A2A2A] text-gray-100 font-bold hover:bg-surface transition-colors active:scale-95 transition-transform">
                    طلب جديد
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
