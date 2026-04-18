<div>
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-100">إدارة الشفتات</h2>
    </div>

    @if(session()->has('success'))
        <div class="p-4 mb-6 rounded-xl bg-emerald-500/20 text-emerald-400 font-medium border border-emerald-500/30">
            {{ session('success') }}
        </div>
    @endif

    @error('shift')
        <div class="p-4 mb-6 rounded-xl bg-red-500/20 text-red-400 font-medium border border-red-500/30">
            {{ $message }}
        </div>
    @enderror

    @if(!$activeShift)
        <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-8 text-center max-w-lg mx-auto mt-12">
            <div class="w-16 h-16 bg-elevated rounded-full flex items-center justify-center mx-auto mb-4 border border-[#2A2A2A]">
                <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-100 mb-2">لا يوجد شفت مفتوح</h3>
            <p class="text-gray-400 mb-6">افتح شفتاً لبدء استقبال الطلبات وتتبع النقدية.</p>
            <button wire:click="openShiftModal" class="px-6 py-3 rounded-xl bg-amber-500 text-black font-bold hover:bg-amber-400 transition-colors w-full shadow-lg shadow-amber-500/20">
                فتح شفت جديد
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-100">الشفت الحالي</h3>
                        <p class="text-gray-400 text-sm mt-1">بدأ بواسطة {{ $activeShift->startedBy->name }}</p>
                    </div>
                    <span class="px-3 py-1 rounded bg-emerald-500/20 text-emerald-400 text-sm font-medium border border-emerald-500/30">
                        مفتوح
                    </span>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-[#2A2A2A]">
                        <span class="text-gray-400">وقت البدء</span>
                        <span class="text-gray-100 font-medium">{{ $activeShift->started_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-[#2A2A2A]">
                        <span class="text-gray-400">النقدية الافتتاحية</span>
                        <span class="text-gray-100 font-medium">${{ number_format($activeShift->starting_cash, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-[#2A2A2A]">
                        <span class="text-gray-400">مبيعات نقدية</span>
                        <span class="text-emerald-400 font-bold">+${{ number_format($expectedCash - $activeShift->starting_cash, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3">
                        <span class="text-gray-400">النقدية المتوقعة في الدرج</span>
                        <span class="text-amber-500 text-xl font-bold">${{ number_format($expectedCash, 2) }}</span>
                    </div>
                </div>

                <button wire:click="closeShiftModal" class="mt-8 px-6 py-3 rounded-xl bg-red-500/20 text-red-500 border border-red-500/30 font-bold hover:bg-red-500/30 transition-colors w-full">
                    إغلاق الشفت
                </button>
            </div>
            
            <div class="bg-surface border border-[#2A2A2A] rounded-2xl p-6 flex flex-col justify-center items-center text-center">
                <div class="w-16 h-16 bg-elevated rounded-full flex items-center justify-center mb-4 border border-[#2A2A2A]">
                    <svg class="w-8 h-8 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-100 mb-2">نقطة البيع جاهزة</h3>
                <p class="text-gray-400 mb-6">النظام جاهز لاستقبال الطلبات.</p>
                <a href="/pos" class="px-6 py-3 rounded-xl bg-elevated text-gray-100 font-bold border border-[#2A2A2A] hover:border-gray-500 transition-colors inline-block">
                    الذهاب لنقطة البيع
                </a>
            </div>
        </div>
    @endif

    {{-- Open Shift Modal --}}
    @if($isOpening)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-surface border border-[#2A2A2A] rounded-2xl w-full max-w-md p-6 shadow-2xl">
            <h3 class="text-xl font-bold text-gray-100 mb-6">فتح شفت</h3>
            
            <form wire:submit.prevent="openShift" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300">مبلغ النقدية الافتتاحية</label>
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input wire:model="startingCash" type="number" step="0.01" class="pr-7 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    @error('startingCash') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" wire:click="$set('isOpening', false)" class="px-4 py-2 rounded-xl text-gray-400 hover:text-gray-100 hover:bg-elevated transition-colors">
                        إلغاء
                    </button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-amber-500 text-black font-bold hover:bg-amber-400 transition-colors">
                        فتح الشفت
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Close Shift Modal --}}
    @if($isClosing)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-surface border border-[#2A2A2A] rounded-2xl w-full max-w-md p-6 shadow-2xl">
            <h3 class="text-xl font-bold text-gray-100 mb-6">إغلاق الشفت</h3>
            
            <div class="bg-base border border-[#2A2A2A] rounded-xl p-4 mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-400">النقدية المتوقعة:</span>
                    <span class="text-xl font-bold text-gray-100">${{ number_format($expectedCash, 2) }}</span>
                </div>
            </div>

            <form wire:submit.prevent="closeShift" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300">النقدية الفعلية عند الإغلاق</label>
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input wire:model.live="endingCash" type="number" step="0.01" class="pr-7 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    @error('endingCash') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
                
                @if(isset($endingCash) && $endingCash !== '')
                    @php $diff = (float)$endingCash - $expectedCash; @endphp
                    @if($diff != 0)
                    <div class="text-sm {{ $diff > 0 ? 'text-emerald-400' : 'text-red-400' }}">
                        الفرق: {{ $diff > 0 ? '+' : '' }}${{ number_format($diff, 2) }}
                    </div>
                    @endif
                @endif

                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" wire:click="$set('isClosing', false)" class="px-4 py-2 rounded-xl text-gray-400 hover:text-gray-100 hover:bg-elevated transition-colors">
                        إلغاء
                    </button>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-red-500 text-white font-bold hover:bg-red-600 transition-colors">
                        تأكيد الإغلاق
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
