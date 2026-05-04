<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white mb-2">مرحباً بعودتك</h2>
        <p class="text-gray-400">سجّل الدخول إلى حسابك للمتابعة</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ url('/houssam/login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">البريد الإلكتروني</label>
            <input 
                id="email" 
                type="email" 
                name="email" 
                value="{{ old('email') }}"
                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition" 
                placeholder="you@example.com"
                required 
                autofocus 
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">كلمة المرور</label>
            <input 
                id="password" 
                type="password" 
                name="password"
                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition" 
                placeholder="••••••••"
                required 
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    class="rounded border-white/10 bg-[#0a1628] text-emerald-500 shadow-sm focus:ring-emerald-500 focus:ring-offset-0 transition" 
                    name="remember"
                >
                <span class="ms-2 text-sm text-gray-400">تذكرني</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-emerald-400 hover:text-emerald-300 transition" href="{{ route('password.request') }}">
                    نسيت كلمة المرور؟
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button 
            type="submit"
            class="w-full py-3 px-4 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition shadow-lg shadow-emerald-500/30"
        >
            تسجيل الدخول
        </button>
    </form>

    <x-slot name="footerText">
        <a href="{{ url('/') }}" class="text-emerald-400 hover:text-emerald-300 transition">
            → العودة للرئيسية
        </a>
    </x-slot>
</x-guest-layout>
