<x-guest-layout>
    <h1 class="text-3xl font-extrabold text-slate-900 mb-2">إنشاء حساب جديد</h1>
    <p class="text-slate-500 mb-8">أدخل بياناتك لإنشاء حساب في النظام</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="block text-sm font-semibold mb-2">الاسم الكامل</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:ring-sky-500"
                   placeholder="د. أحمد محمد">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold mb-2">البريد الإلكتروني</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" dir="ltr"
                   class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-left focus:border-sky-500 focus:ring-sky-500"
                   placeholder="name@clinic.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold mb-2">كلمة المرور</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" dir="ltr"
                   class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-left focus:border-sky-500 focus:ring-sky-500">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-semibold mb-2">تأكيد كلمة المرور</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" dir="ltr"
                   class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-left focus:border-sky-500 focus:ring-sky-500">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit"
                class="w-full rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold py-3 shadow-lg shadow-sky-600/30 transition">
            إنشاء الحساب
        </button>

        <p class="text-center text-sm text-slate-500">
            عندك حساب؟
            <a href="{{ route('login') }}" class="text-sky-600 font-semibold hover:text-sky-800">تسجيل الدخول</a>
        </p>
    </form>
</x-guest-layout>
