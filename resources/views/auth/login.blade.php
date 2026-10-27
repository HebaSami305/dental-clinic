<x-guest-layout>
    <h1 class="text-3xl font-extrabold text-slate-900 mb-2">أهلاً بعودتك 👋</h1>
    <p class="text-slate-500 mb-8">سجّل دخولك للوصول إلى لوحة التحكم</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold mb-2">البريد الإلكتروني</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" dir="ltr"
                   class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-left focus:border-sky-500 focus:ring-sky-500"
                   placeholder="name@clinic.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold mb-2">كلمة المرور</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" dir="ltr"
                   class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-left focus:border-sky-500 focus:ring-sky-500"
                   placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                       class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                <span class="text-sm text-slate-600">تذكرني</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-sky-600 hover:text-sky-800 font-semibold">
                    نسيت كلمة المرور؟
                </a>
            @endif
        </div>

        <button type="submit"
                class="w-full rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold py-3 shadow-lg shadow-sky-600/30 transition">
            تسجيل الدخول
        </button>

        @if (Route::has('register'))
            <p class="text-center text-sm text-slate-500">
                ما عندك حساب؟
                <a href="{{ route('register') }}" class="text-sky-600 font-semibold hover:text-sky-800">إنشاء حساب</a>
            </p>
        @endif
    </form>
</x-guest-layout>