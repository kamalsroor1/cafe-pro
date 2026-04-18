<aside class="w-64 bg-surface border-r border-[#2A2A2A] flex flex-col transition-all duration-300">
    <div class="p-4 flex items-center gap-3 border-b border-[#2A2A2A]">
        <span class="text-amber-500 text-2xl">☕</span>
        <span class="font-bold text-gray-100 text-lg whitespace-nowrap">Cafe Pro</span>
    </div>

    <nav class="flex-1 py-2 overflow-y-auto">
        <a href="/" class="flex items-center gap-3 px-4 min-h-[56px] text-gray-400 hover:text-gray-100 hover:bg-elevated border-l-4 border-transparent transition-all duration-200">
            <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-sm font-medium">Dashboard</span>
        </a>

        @can('manage products')
        <a href="/products" class="flex items-center gap-3 px-4 min-h-[56px] {{ request()->is('products*') ? 'border-amber-500 text-amber-400 bg-elevated border-l-4' : 'text-gray-400 hover:text-gray-100 hover:bg-elevated border-l-4 border-transparent' }} transition-all duration-200">
            <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span class="text-sm font-medium">Products</span>
        </a>
        @endcan

        @can('manage ingredients')
        <a href="/inventory" class="flex items-center gap-3 px-4 min-h-[56px] {{ request()->is('inventory*') ? 'border-amber-500 text-amber-400 bg-elevated border-l-4' : 'text-gray-400 hover:text-gray-100 hover:bg-elevated border-l-4 border-transparent' }} transition-all duration-200">
            <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            <span class="text-sm font-medium">Inventory</span>
        </a>
        @endcan
        
        @can('access pos')
        <a href="/shifts" class="flex items-center gap-3 px-4 min-h-[56px] {{ request()->is('shifts*') ? 'border-amber-500 text-amber-400 bg-elevated border-l-4' : 'text-gray-400 hover:text-gray-100 hover:bg-elevated border-l-4 border-transparent' }} transition-all duration-200">
            <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-sm font-medium">Shifts</span>
        </a>
        <a href="/pos" class="flex items-center gap-3 px-4 min-h-[56px] {{ request()->is('pos*') ? 'border-amber-500 text-amber-400 bg-elevated border-l-4' : 'text-gray-400 hover:text-gray-100 hover:bg-elevated border-l-4 border-transparent' }} transition-all duration-200">
            <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            <span class="text-sm font-medium">POS Terminal</span>
        </a>
        <a href="/orders" class="flex items-center gap-3 px-4 min-h-[56px] {{ request()->is('orders*') ? 'border-amber-500 text-amber-400 bg-elevated border-l-4' : 'text-gray-400 hover:text-gray-100 hover:bg-elevated border-l-4 border-transparent' }} transition-all duration-200">
            <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            <span class="text-sm font-medium">Orders History</span>
        </a>
        @endcan

        @can('view kds')
        <a href="/kds" class="flex items-center gap-3 px-4 min-h-[56px] {{ request()->is('kds*') ? 'border-amber-500 text-amber-400 bg-elevated border-l-4' : 'text-gray-400 hover:text-gray-100 hover:bg-elevated border-l-4 border-transparent' }} transition-all duration-200">
            <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
            <span class="text-sm font-medium">Kitchen (KDS)</span>
        </a>
        @endcan
    </nav>

    <div class="p-4 border-t border-[#2A2A2A]">
        @auth
        <div class="flex items-center justify-between">
            <span class="text-gray-400 text-sm truncate">{{ auth()->user()->name }}</span>
            <form method="POST" action="/logout" class="inline">
                @csrf
                <button type="submit" class="text-red-400 hover:text-red-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
        @endauth
    </div>
</aside>
