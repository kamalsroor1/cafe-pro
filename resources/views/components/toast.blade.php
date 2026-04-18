<div 
    x-data="{ show: false, message: '', type: 'success' }"
    x-on:toast-message.window="
        message = $event.detail.message;
        type = $event.detail.type || 'success';
        show = true;
        setTimeout(() => show = false, 3000);
    "
    class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 pointer-events-none"
>
    <div 
        x-show="show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="px-6 py-3 rounded-xl shadow-lg font-bold flex items-center gap-3 pointer-events-auto"
        :class="{
            'bg-emerald-500 text-white': type === 'success',
            'bg-red-500 text-white': type === 'error',
            'bg-amber-500 text-black': type === 'warning'
        }"
        style="display: none;"
    >
        <span x-text="message"></span>
    </div>
</div>

{{-- Global listener for Livewire session flash messages if needed --}}
@if(session()->has('success'))
<div x-data x-init="$dispatch('toast-message', { message: '{{ session('success') }}', type: 'success' })"></div>
@endif
@if(session()->has('error'))
<div x-data x-init="$dispatch('toast-message', { message: '{{ session('error') }}', type: 'error' })"></div>
@endif
