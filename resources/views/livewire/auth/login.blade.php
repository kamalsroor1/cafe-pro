{{-- MOBILE RESPONSIVE: login.blade.php --}}
<div class="flex items-center justify-center min-h-screen bg-base p-4">
    <div class="w-full max-w-md p-6 md:p-8 bg-surface rounded-3xl shadow-2xl shadow-black/50 border border-[#2A2A2A]">
        <div class="text-center mb-8">
            <span class="text-amber-500 text-6xl inline-block mb-4">☕</span>
            <h2 class="text-2xl md:text-3xl font-black text-gray-100">مرحباً بك في Cafe Pro</h2>
            <p class="text-sm md:text-base text-gray-400 mt-2 font-medium">يرجى تسجيل الدخول إلى حسابك</p>
        </div>

        <form wire:submit.prevent="login" class="space-y-5">
            <div>
                <label for="email" class="block text-sm md:text-base font-bold text-gray-300 mb-2">البريد الإلكتروني</label>
                <div class="mt-1">
                    <input wire:model="email" id="email" type="email" required
                        class="min-h-[56px] w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-3 text-lg text-gray-100 focus:ring-amber-500 focus:border-amber-500" dir="ltr">
                </div>
                @error('email') <span class="text-red-400 text-sm mt-2 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm md:text-base font-bold text-gray-300 mb-2">كلمة المرور</label>
                <div class="mt-1">
                    <input wire:model="password" id="password" type="password" required
                        class="min-h-[56px] w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-3 text-lg text-gray-100 focus:ring-amber-500 focus:border-amber-500" dir="ltr">
                </div>
            </div>

            <button type="submit"
                class="min-h-[64px] mt-8 w-full flex justify-center items-center gap-2 py-4 px-4 border border-transparent rounded-xl shadow-lg shadow-amber-500/20 text-xl font-black text-black bg-amber-500 hover:bg-amber-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all active:scale-[0.98]">
                <span wire:loading.remove>تسجيل الدخول</span>
                <span wire:loading>جاري الدخول...</span>
            </button>
        </form>
    </div>
</div>
{{-- 
  CHANGES:
  - Scaled up input borders, fonts and padding.
  - Inputs use min-h-[56px] for easy finger targeting during login.
  - Submit button explicitly set to min-h-[64px] with text-xl.
--}}
