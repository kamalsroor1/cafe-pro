<div class="flex items-center justify-center min-h-screen bg-base">
    <div class="w-full max-w-md p-8 bg-surface rounded-2xl shadow-xl border border-[#2A2A2A]">
        <div class="text-center mb-8">
            <span class="text-amber-500 text-5xl inline-block mb-4">☕</span>
            <h2 class="text-2xl font-bold text-gray-100">مرحباً بك في Cafe Pro</h2>
            <p class="text-gray-400 mt-2">يرجى تسجيل الدخول إلى حسابك</p>
        </div>

        <form wire:submit.prevent="login" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300">البريد الإلكتروني</label>
                <div class="mt-2">
                    <input wire:model="email" id="email" type="email" required
                        class="w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-3 text-gray-100 focus:ring-amber-500 focus:border-amber-500" dir="ltr">
                </div>
                @error('email') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300">كلمة المرور</label>
                <div class="mt-2">
                    <input wire:model="password" id="password" type="password" required
                        class="w-full bg-base border border-[#2A2A2A] rounded-xl px-4 py-3 text-gray-100 focus:ring-amber-500 focus:border-amber-500" dir="ltr">
                </div>
            </div>

            <button type="submit"
                class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-black bg-amber-500 hover:bg-amber-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors duration-200 active:scale-95 transition-transform">
                <span wire:loading.remove>تسجيل الدخول</span>
                <span wire:loading>جاري الدخول...</span>
            </button>
        </form>
    </div>
</div>
