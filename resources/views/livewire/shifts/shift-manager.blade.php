{{-- MOBILE RESPONSIVE: shift-manager.blade.php --}}
<div>
    <div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h2 class="text-xl md:text-2xl font-bold text-gray-100">إدارة الشفتات</h2>
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
        <div class="bg-surface border border-[#2A2A2A] rounded-3xl p-6 md:p-8 text-center max-w-lg mx-auto md:mt-12">
            <div class="w-16 h-16 bg-elevated rounded-full flex items-center justify-center mx-auto mb-4 border border-[#2A2A2A]">
                <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-100 mb-2">لا يوجد شفت مفتوح</h3>
            <p class="text-sm md:text-base text-gray-400 mb-6">افتح شفتاً لبدء استقبال الطلبات وتتبع النقدية.</p>
            <button wire:click="openShiftModal" class="min-h-[56px] px-6 rounded-xl bg-amber-500 text-black font-bold hover:bg-amber-400 transition-colors w-full shadow-lg shadow-amber-500/20 active:scale-95 text-lg">
                فتح شفت جديد
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div class="bg-surface border border-[#2A2A2A] rounded-2xl md:rounded-3xl p-4 md:p-6">
                <div class="flex justify-between items-start mb-6 border-b border-[#2A2A2A] pb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-100">الشفت الحالي</h3>
                        <p class="text-gray-400 text-sm mt-1">بدأ بواسطة {{ $activeShift->startedBy->name }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold border border-emerald-500/30">
                        مفتوح
                    </span>
                </div>

                <div class="space-y-1">
                    <div class="flex justify-between items-center py-3 border-b border-[#2A2A2A]/50">
                        <span class="text-sm md:text-base text-gray-400">وقت البدء</span>
                        <span class="text-sm md:text-base text-gray-100 font-medium">{{ $activeShift->started_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-[#2A2A2A]/50">
                        <span class="text-sm md:text-base text-gray-400">النقدية الافتتاحية</span>
                        <span class="text-sm md:text-base text-gray-100 font-medium">${{ number_format($activeShift->starting_cash, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-[#2A2A2A]/50">
                        <span class="text-sm md:text-base text-gray-400">مبيعات نقدية</span>
                        <span class="text-sm md:text-base text-emerald-400 font-bold">+${{ number_format($expectedCash - $activeShift->starting_cash, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-4 bg-elevated px-4 rounded-xl mt-4">
                        <span class="text-sm md:text-base text-gray-300 font-medium">الدرج (النقدية المتوقعة)</span>
                        <span class="text-amber-500 text-xl md:text-2xl font-black">${{ number_format($expectedCash, 2) }}</span>
                    </div>
                </div>

                <button wire:click="closeShiftModal" class="mt-6 min-h-[56px] px-6 rounded-xl bg-red-500/20 text-red-500 border border-red-500/30 font-bold hover:bg-red-500/30 transition-colors w-full active:scale-95 text-lg">
                    إغلاق الشفت
                </button>
            </div>
            
            <div class="bg-surface border border-[#2A2A2A] rounded-2xl md:rounded-3xl p-6 flex flex-col justify-center items-center text-center">
                <div class="w-16 h-16 bg-amber-500/10 rounded-full flex items-center justify-center mb-4 border border-amber-500/20">
                    <svg class="w-8 h-8 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h3 class="text-xl md:text-2xl font-bold text-gray-100 mb-2">نقطة البيع جاهزة</h3>
                <p class="text-gray-400 mb-6 text-sm md:text-base">النظام جاهز لاستقبال الطلبات وتسجيلها.</p>
                <a href="/pos" class="min-h-[56px] px-8 flex justify-center items-center rounded-xl bg-elevated text-gray-100 font-bold border border-[#2A2A2A] hover:border-gray-500 hover:text-white transition-colors active:scale-95 w-full md:w-auto text-lg">
                    الذهاب لنقطة البيع
                </a>
            </div>
        </div>
    @endif

    {{-- Open Shift Modal --}}
    @if($isOpening)
    <div class="fixed inset-0 z-50 flex items-end md:items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="bg-surface border border-[#2A2A2A] rounded-3xl w-full max-w-md p-6 shadow-2xl relative translate-y-0 text-right">
            <h3 class="text-xl font-bold text-gray-100 mb-6">فتح شفت جديد</h3>
            
            <form wire:submit.prevent="openShift" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">مبلغ النقدية الافتتاحية في الدرج</label>
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 font-bold">$</span>
                        </div>
                        <input wire:model="startingCash" type="number" step="0.01" inputmode="decimal" class="min-h-[56px] pr-8 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 text-lg focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    @error('startingCash') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex flex-col-reverse md:flex-row justify-end gap-3 mt-8">
                    <button type="button" wire:click="$set('isOpening', false)" class="min-h-[56px] w-full md:w-auto px-6 rounded-xl text-gray-400 hover:text-gray-100 bg-elevated border border-[#2A2A2A] hover:bg-surface transition-colors font-bold">
                        إلغاء
                    </button>
                    <button type="submit" class="min-h-[56px] w-full md:w-auto px-8 rounded-xl bg-amber-500 text-black font-black hover:bg-amber-400 shadow-lg shadow-amber-500/20 active:scale-95 transition-all">
                        تأكيد وفتح
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Close Shift Modal --}}
    @if($isClosing)
    <div class="fixed inset-0 z-50 flex items-end md:items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="bg-surface border border-[#2A2A2A] rounded-3xl w-full max-w-md p-6 shadow-2xl relative translate-y-0 text-right">
            <h3 class="text-xl font-bold text-gray-100 mb-6">إغلاق الشفت الحالي</h3>
            
            <div class="bg-base border border-[#2A2A2A] rounded-xl p-4 mb-6 text-center">
                <span class="text-gray-400 text-sm block mb-1">النقدية المتوقعة في الدرج حالياً:</span>
                <span class="text-3xl font-black text-amber-500">${{ number_format($expectedCash, 2) }}</span>
            </div>

            <form wire:submit.prevent="closeShift" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">قم بعد الدرج وأدخل النقدية الفعلية</label>
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 font-bold">$</span>
                        </div>
                        <input wire:model.live="endingCash" type="number" step="0.01" inputmode="decimal" class="min-h-[56px] pr-8 w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 text-lg focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    @error('endingCash') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                @if(isset($endingCash) && $endingCash !== '')
                    @php $diff = (float)$endingCash - $expectedCash; @endphp
                    @if($diff != 0)
                    <div class="text-base font-bold text-center p-3 rounded-lg border {{ $diff > 0 ? 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20' : 'text-red-400 bg-red-500/10 border-red-500/20' }}">
                        الفرق/العجز: <span dir="ltr">{{ $diff > 0 ? '+' : '' }}${{ number_format($diff, 2) }}</span>
                    </div>
                    @else
                    <div class="text-base font-bold text-center p-3 rounded-lg border text-emerald-400 bg-emerald-500/10 border-emerald-500/20">
                        تطابق تام في النقدية
                    </div>
                    @endif
                @endif

                <div class="flex flex-col-reverse md:flex-row justify-end gap-3 mt-8">
                    <button type="button" wire:click="$set('isClosing', false)" class="min-h-[56px] w-full md:w-auto px-6 rounded-xl text-gray-400 hover:text-gray-100 bg-elevated border border-[#2A2A2A] hover:bg-surface transition-colors font-bold">
                        إلغاء الأمر
                    </button>
                    <button type="submit" class="min-h-[56px] w-full md:w-auto px-8 rounded-xl bg-red-500/90 text-white font-black hover:bg-red-500 shadow-lg shadow-red-500/20 active:scale-95 transition-all">
                        تأكيد إغلاق الشفت
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
{{-- 
  CHANGES:
  - Ensured modals pop up properly bottom-aligned on mobile and centered on md+.
  - Enlarged inputs and buttons ensuring min-h-[48px] (many to 56px).
  - Improved typographic scale for readabilty.
  - Added inputmode="decimal" for better numeric keyboards on mobile devices.
--}}
