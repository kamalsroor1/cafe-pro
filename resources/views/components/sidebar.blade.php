{{-- MOBILE RESPONSIVE: sidebar.blade.php --}}
<aside x-data="{ collapsed: false }" 
       :class="{
           'translate-x-0 w-72': sidebarOpen,
           'translate-x-full w-72': !sidebarOpen,
           'lg:translate-x-0': true,
           'lg:w-20': collapsed,
           'lg:w-64': !collapsed
       }"
       class="fixed lg:static inset-y-0 right-0 z-50 bg-surface border-l lg:border-l-0 lg:border-r border-[#2A2A2A] flex flex-col transition-all duration-300 transform lg:transform-none">
    
    {{-- Desktop Collapse Button (Hidden on Mobile) --}}
    <button @click="collapsed = !collapsed" class="hidden lg:block absolute -left-3 top-6 bg-elevated border border-[#2A2A2A] rounded-full p-1 text-gray-400 hover:text-white z-10 transition-transform" :class="collapsed ? 'rotate-180' : ''">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
    </button>

    {{-- Mobile Close Drawer Button --}}
    <div class="lg:hidden absolute left-4 top-4">
        <button @click="sidebarOpen = false" class="min-h-[48px] min-w-[48px] flex items-center justify-center text-gray-400 hover:text-white rounded-lg bg-elevated border border-[#2A2A2A]">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <div class="p-4 flex items-center gap-3 border-b border-[#2A2A2A] overflow-hidden whitespace-nowrap min-h-[64px]">
        <span class="text-amber-500 text-2xl shrink-0">☕</span>
        <span x-show="!collapsed || sidebarOpen" class="font-bold text-gray-100 text-lg">Cafe Pro</span>
    </div>

    <nav class="flex-1 py-2 overflow-y-auto overflow-x-hidden">
        <a href="/" class="flex items-center gap-3 px-4 min-h-[56px] text-gray-400 hover:text-gray-100 hover:bg-elevated border-r-4 border-transparent transition-all duration-200" title="لوحة التحكم">
            <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span x-show="!collapsed || sidebarOpen" class="text-base lg:text-sm font-medium whitespace-nowrap">لوحة التحكم</span>
        </a>

        @can('manage products')
        <a href="/products" class="flex items-center gap-3 px-4 min-h-[56px] {{ request()->is('products*') ? 'border-amber-500 text-amber-400 bg-elevated border-r-4' : 'text-gray-400 hover:text-gray-100 hover:bg-elevated border-r-4 border-transparent' }} transition-all duration-200" title="المنتجات">
            <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span x-show="!collapsed || sidebarOpen" class="text-base lg:text-sm font-medium whitespace-nowrap">المنتجات</span>
        </a>
        @endcan

        @can('manage ingredients')
        <a href="/inventory" class="flex items-center gap-3 px-4 min-h-[56px] {{ request()->is('inventory*') ? 'border-amber-500 text-amber-400 bg-elevated border-r-4' : 'text-gray-400 hover:text-gray-100 hover:bg-elevated border-r-4 border-transparent' }} transition-all duration-200" title="المخزون">
            <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            <span x-show="!collapsed || sidebarOpen" class="text-base lg:text-sm font-medium whitespace-nowrap">المخزون</span>
        </a>
        @endcan
        
        @can('access pos')
        <a href="/shifts" class="flex items-center gap-3 px-4 min-h-[56px] {{ request()->is('shifts*') ? 'border-amber-500 text-amber-400 bg-elevated border-r-4' : 'text-gray-400 hover:text-gray-100 hover:bg-elevated border-r-4 border-transparent' }} transition-all duration-200" title="الشفتات">
            <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span x-show="!collapsed || sidebarOpen" class="text-base lg:text-sm font-medium whitespace-nowrap">الشفتات</span>
        </a>
        <a href="/pos" class="flex items-center gap-3 px-4 min-h-[56px] {{ request()->is('pos*') ? 'border-amber-500 text-amber-400 bg-elevated border-r-4' : 'text-gray-400 hover:text-gray-100 hover:bg-elevated border-r-4 border-transparent' }} transition-all duration-200" title="نقطة البيع">
            <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            <span x-show="!collapsed || sidebarOpen" class="text-base lg:text-sm font-medium whitespace-nowrap">نقطة البيع (POS)</span>
        </a>
        <a href="/orders" class="flex items-center gap-3 px-4 min-h-[56px] {{ request()->is('orders*') ? 'border-amber-500 text-amber-400 bg-elevated border-r-4' : 'text-gray-400 hover:text-gray-100 hover:bg-elevated border-r-4 border-transparent' }} transition-all duration-200" title="سجل الطلبات">
            <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            <span x-show="!collapsed || sidebarOpen" class="text-base lg:text-sm font-medium whitespace-nowrap">سجل الطلبات</span>
        </a>
        @endcan

        @can('view kds')
        <a href="/kds" class="flex items-center gap-3 px-4 min-h-[56px] {{ request()->is('kds*') ? 'border-amber-500 text-amber-400 bg-elevated border-r-4' : 'text-gray-400 hover:text-gray-100 hover:bg-elevated border-r-4 border-transparent' }} transition-all duration-200" title="شاشة المطبخ">
            <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
            <span x-show="!collapsed || sidebarOpen" class="text-base lg:text-sm font-medium whitespace-nowrap">شاشة المطبخ (KDS)</span>
        </a>
        @endcan

        @can('manage expenses')
        <a href="/expenses" class="flex items-center gap-3 px-4 min-h-[56px] {{ request()->is('expenses*') ? 'border-amber-500 text-amber-400 bg-elevated border-r-4' : 'text-gray-400 hover:text-gray-100 hover:bg-elevated border-r-4 border-transparent' }} transition-all duration-200" title="المصروفات">
            <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span x-show="!collapsed || sidebarOpen" class="text-base lg:text-sm font-medium whitespace-nowrap">المصروفات</span>
        </a>
        @endcan

        @can('view reports')
        <a href="/reports/profit" class="flex items-center gap-3 px-4 min-h-[56px] {{ request()->is('reports/profit*') ? 'border-amber-500 text-amber-400 bg-elevated border-r-4' : 'text-gray-400 hover:text-gray-100 hover:bg-elevated border-r-4 border-transparent' }} transition-all duration-200" title="تقرير الأرباح">
            <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <span x-show="!collapsed || sidebarOpen" class="text-base lg:text-sm font-medium whitespace-nowrap">تقرير الأرباح</span>
        </a>
        <a href="/reports/shifts" class="flex items-center gap-3 px-4 min-h-[56px] {{ request()->is('reports/shifts*') ? 'border-amber-500 text-amber-400 bg-elevated border-r-4' : 'text-gray-400 hover:text-gray-100 hover:bg-elevated border-r-4 border-transparent' }} transition-all duration-200" title="تقرير الشفتات">
            <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span x-show="!collapsed || sidebarOpen" class="text-base lg:text-sm font-medium whitespace-nowrap">تقرير الشفتات</span>
        </a>
        @endcan
    </nav>

    <div class="p-4 border-t border-[#2A2A2A] overflow-hidden">
        @auth
        <div class="flex items-center" :class="(!collapsed || sidebarOpen) ? 'justify-between' : 'justify-center'">
            <span x-show="!collapsed || sidebarOpen" class="text-gray-400 text-sm truncate whitespace-nowrap">{{ auth()->user()->name }}</span>
            <form method="POST" action="/logout" class="inline shrink-0">
                @csrf
                <button type="submit" class="text-red-400 hover:text-red-300 min-h-[48px] min-w-[48px] flex justify-center items-center rounded-lg" title="تسجيل خروج">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
        @endauth
    </div>
</aside>
{{-- 
  CHANGES:
  - Sidebar converted to mobile slide-in drawer using fixed positioning.
  - Sidebar connects to body's sidebarOpen state for mobile toggle.
  - Kept lg:static and widths for lg: view (64/20).
  - Increased font-size to text-base on mobile menu items.
  - Ensured icons, buttons, logout have min-h-[48px] min-w-[48px].
--}}
