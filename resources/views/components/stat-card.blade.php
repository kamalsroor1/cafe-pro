<div class="bg-surface rounded-xl border border-[#2A2A2A] p-6 flex items-center gap-4">
    <div class="p-3 rounded-xl bg-elevated text-amber-500">
        {{ $icon ?? '' }}
    </div>
    <div>
        <p class="text-sm font-medium text-gray-400">{{ $title }}</p>
        <p class="text-2xl font-bold text-gray-100">{{ $value }}</p>
    </div>
</div>
