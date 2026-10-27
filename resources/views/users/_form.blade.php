@php
    $input = 'w-full rounded-xl border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:ring-sky-500';
    $isSelf = isset($user) && $user->id === auth()->id();
@endphp

<div class="grid sm:grid-cols-2 gap-5">
    <div>
        <label class="block text-sm font-semibold mb-2">الاسم *</label>
        <input type="text" name="name" required value="{{ old('name', $user->name ?? '') }}" class="{{ $input }}">
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <label class="block text-sm font-semibold mb-2">البريد الإلكتروني *</label>
        <input type="email" name="email" dir="ltr" required value="{{ old('email', $user->email ?? '') }}" class="{{ $input }} text-left">
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <label class="block text-sm font-semibold mb-2">الدور *</label>
        <select name="role" class="{{ $input }}" @disabled($isSelf)>
            @foreach (config('permissions.roles') as $key => $label)
                <option value="{{ $key }}" @selected(old('role', $user->role ?? 'secretary') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        @if ($isSelf) <p class="text-xs text-slate-400 mt-1">ما بتقدر تغيّر دور حسابك</p> @endif
        <x-input-error :messages="$errors->get('role')" class="mt-2" />
    </div>

    <div>
        <label class="block text-sm font-semibold mb-2">
            كلمة المرور {{ isset($user) ? '(اتركها فاضية لعدم التغيير)' : '*' }}
        </label>
        <input type="password" name="password" dir="ltr" autocomplete="new-password"
               @required(! isset($user)) class="{{ $input }} text-left">
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>
</div>

<div class="flex items-center gap-3 mt-8">
    <button type="submit" class="rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold px-8 py-3 shadow-lg shadow-sky-600/30 transition">حفظ</button>
    <a href="{{ route('users.index') }}" class="rounded-xl bg-slate-100 hover:bg-slate-200 font-bold px-8 py-3 transition">إلغاء</a>
</div>