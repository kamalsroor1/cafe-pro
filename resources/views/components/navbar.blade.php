<header class="h-16 bg-surface border-b border-[#2A2A2A] flex items-center justify-between px-6">
    <div class="flex items-center gap-4">
        <button class="text-gray-400 hover:text-gray-100 p-2 rounded-lg hover:bg-elevated transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <h1 class="text-gray-100 font-semibold text-lg">{{ $title ?? '' }}</h1>
    </div>

    <div class="flex items-center gap-4">
        {{-- Shift Status --}}
        @php
            $activeShift = \App\Models\Shift::where('status', 'open')->first();
        @endphp

        @if($activeShift)
            <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                الشفت مفتوح
            </span>
        @else
            <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-500/20 text-red-400 border border-red-500/30">
                لا يوجد شفت مفتوح
            </span>
        @endif
    </div>
</header>
