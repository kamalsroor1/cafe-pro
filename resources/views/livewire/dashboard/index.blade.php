{{-- MOBILE RESPONSIVE: index.blade.php --}}
<div>
    <div class="mb-6 lg:mb-8">
        <h1 class="text-xl md:text-2xl lg:text-3xl font-medium lg:font-bold text-gray-100">لوحة التحكم</h1>
        <p class="text-sm md:text-base text-gray-400 mt-1">مرحباً بعودتك، {{ auth()->user()->name }}</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 lg:gap-6">
        <div class="bg-surface border border-[#2A2A2A] rounded-xl p-4 md:p-6 flex flex-col md:flex-row items-start md:items-center gap-3 md:gap-4">
            <div class="p-3 rounded-xl bg-elevated text-amber-500 shrink-0">
                <svg class="w-8 h-8 md:w-10 md:h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-400">إجمالي المنتجات</p>
                <p class="text-2xl md:text-3xl font-bold text-gray-100">{{ $totalProducts }}</p>
            </div>
        </div>

        <div class="bg-surface border border-[#2A2A2A] rounded-xl p-4 md:p-6 flex flex-col md:flex-row items-start md:items-center gap-3 md:gap-4">
            <div class="p-3 rounded-xl bg-elevated text-emerald-500 shrink-0">
                <svg class="w-8 h-8 md:w-10 md:h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-400">مبيعات اليوم</p>
                <p class="text-2xl md:text-3xl font-bold text-gray-100">${{ number_format($todaySummary['sales'], 2) }}</p>
            </div>
        </div>

        <div class="bg-surface border border-[#2A2A2A] rounded-xl p-4 md:p-6 flex flex-col md:flex-row items-start md:items-center gap-3 md:gap-4">
            <div class="p-3 rounded-xl bg-elevated text-blue-500 shrink-0">
                <svg class="w-8 h-8 md:w-10 md:h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-400">إجمالي الطلبات</p>
                <p class="text-2xl md:text-3xl font-bold text-gray-100">{{ $todaySummary['orders_count'] }}</p>
            </div>
        </div>

        <div class="bg-surface border border-[#2A2A2A] rounded-xl p-4 md:p-6 flex flex-col md:flex-row items-start md:items-center gap-3 md:gap-4">
            <div class="p-3 rounded-xl bg-elevated shrink-0 {{ $todaySummary['net_profit'] >= 0 ? 'text-emerald-500' : 'text-red-500' }}">
                <svg class="w-8 h-8 md:w-10 md:h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-400">صافي الربح</p>
                <p class="text-2xl md:text-3xl font-bold {{ $todaySummary['net_profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                    {{ $todaySummary['net_profit'] >= 0 ? '+' : '' }}${{ number_format($todaySummary['net_profit'], 2) }}
                </p>
            </div>
        </div>
    </div>

    <div class="mt-8 bg-surface border border-[#2A2A2A] rounded-xl p-4 md:p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <h3 class="text-lg font-medium md:font-bold text-gray-100">التفاصيل المالية (اليوم)</h3>
            <a href="{{ route('reports.profit') }}" class="min-h-[48px] w-full md:w-auto px-4 py-2 flex items-center justify-center bg-amber-500/10 text-amber-500 hover:text-amber-400 hover:bg-amber-500/20 rounded-lg text-sm md:text-base font-medium transition-colors">
                عرض التقرير الكامل
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-6">
            <div class="p-4 bg-base border border-[#2A2A2A] rounded-xl flex items-center justify-between md:block">
                <p class="text-sm md:text-base text-gray-400 mb-0 md:mb-1">تكلفة البضاعة</p>
                <p class="text-xl md:text-2xl font-bold text-gray-100">${{ number_format($todaySummary['cogs'], 2) }}</p>
            </div>
            <div class="p-4 bg-base border border-[#2A2A2A] rounded-xl flex items-center justify-between md:block">
                <p class="text-sm md:text-base text-gray-400 mb-0 md:mb-1">المصروفات</p>
                <p class="text-xl md:text-2xl font-bold text-gray-100">${{ number_format($todaySummary['expenses'], 2) }}</p>
            </div>
            <div class="p-4 bg-base border border-[#2A2A2A] rounded-xl flex items-center justify-between md:block">
                <p class="text-sm md:text-base text-gray-400 mb-0 md:mb-1">قيمة الهالك</p>
                <p class="text-xl md:text-2xl font-bold text-amber-500">${{ number_format($todaySummary['wastage'], 2) }}</p>
            </div>
        </div>
    </div>
</div>
{{-- 
  CHANGES:
  - Adjusted stat grids: grid-cols-2 gap-3 on mobile.
  - Typographic scale adjusted: smaller labels, bigger stats on md+.
  - Icon sizes md+ variations added.
  - View report link and table-like rows converted to cards.
--}}
