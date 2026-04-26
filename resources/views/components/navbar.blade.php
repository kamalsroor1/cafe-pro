{{-- MOBILE RESPONSIVE: navbar.blade.php --}}
<header class="h-14 lg:h-16 bg-surface border-b border-[#2A2A2A] flex items-center justify-between px-4 lg:px-6">
    <div class="flex items-center gap-3 w-full lg:w-auto">
        {{-- Mobile Hamburger menu button --}}
        <button @click="sidebarOpen = true" class="lg:hidden text-gray-400 hover:text-gray-100 min-h-[48px] min-w-[48px] rounded-lg hover:bg-elevated transition-colors flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        {{-- Title Desktop only button representation --}}
        <h1 class="text-gray-100 font-semibold text-lg lg:text-xl truncate flex-1 lg:flex-none">{{ $title ?? '' }}</h1>
    </div>

    <div class="flex items-center gap-3">
        {{-- Shift Status --}}
        @php
            $activeShift = \App\Models\Shift::where('status', 'open')->first();
        @endphp

        @if($activeShift)
            <span class="px-3 py-2 lg:py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 whitespace-nowrap">
                الشفت مفتوح
            </span>
        @else
            <span class="px-3 py-2 lg:py-1 rounded-full text-xs font-medium bg-red-500/20 text-red-400 border border-red-500/30 whitespace-nowrap">
                لا يوجد شفت
            </span>
        @endif
    </div>
</header>
{{-- 
  CHANGES:
  - Added Hamburger menu toggle for mobile drawer (triggers sidebarOpen=true).
  - Adjusted Navbar height to h-14 on mobile and h-16 on lg.
  - Ensured touch targets are 48px minimum.
  - Fixed flex container and truncation for small screens.
--}}
