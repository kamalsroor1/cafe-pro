@props(['status', 'type' => 'info'])

@php
    $colors = [
        'success' => 'bg-emerald-500/20 text-emerald-400',
        'danger' => 'bg-red-500/20 text-red-400',
        'warning' => 'bg-amber-500/20 text-amber-400',
        'info' => 'bg-blue-500/20 text-blue-400',
    ];

    $colorClass = $colors[$type] ?? $colors['info'];
@endphp

<span class="px-2 py-1 rounded {{ $colorClass }} text-xs font-medium">
    {{ $status }}
</span>
