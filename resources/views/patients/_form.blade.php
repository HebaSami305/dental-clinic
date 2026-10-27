@php
    $input = 'w-full rounded-xl border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:ring-sky-500';
@endphp

<div class="grid sm:grid-cols-2 gap-5">
    <div>
        <label class="block text-sm font-semibold mb-2">الاسم الكامل *</label>
        <input type="text" name="name" value="{{ old('name', $patient->name ?? '') }}" required class="{{ $input }}">
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <label class="block text-sm font-semibold mb-2">رقم الهاتف *</label>
        <input type="text" name="phone" dir="ltr" value="{{ old('phone', $patient->phone ?? '') }}" required class="{{ $input }} text-left">
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>

    <div>
        <label class="block text-sm font-semibold mb-2">الجنس *</label>
        <select name="gender" required class="{{ $input }}">
            <option value="male" @selected(old('gender', $patient->gender ?? '') === 'male')>ذكر</option>
            <option value="female" @selected(old('gender', $patient->gender ?? '') === 'female')>أنثى</option>
        </select>
        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
    </div>

    <div>
        <label class="block text-sm font-semibold mb-2">تاريخ الميلاد</label>
        <input type="date" name="birth_date" dir="ltr"
               value="{{ old('birth_date', optional($patient->birth_date ?? null)->format('Y-m-d')) }}" class="{{ $input }}">
        <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold mb-2">العنوان</label>
        <input type="text" name="address" value="{{ old('address', $patient->address ?? '') }}" class="{{ $input }}">
        <x-input-error :messages="$errors->get('address')" class="mt-2" />
    </div>

    <div>
        <label class="block text-sm font-semibold mb-2">الحساسية</label>
        <textarea name="allergies" rows="3" class="{{ $input }}" placeholder="مثال: البنسلين، اللاتكس">{{ old('allergies', $patient->allergies ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('allergies')" class="mt-2" />
    </div>

    <div>
        <label class="block text-sm font-semibold mb-2">التاريخ المرضي</label>
        <textarea name="medical_history" rows="3" class="{{ $input }}" placeholder="أمراض مزمنة، عمليات سابقة...">{{ old('medical_history', $patient->medical_history ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('medical_history')" class="mt-2" />
    </div>
</div>

<div class="flex items-center gap-3 mt-8">
    <button type="submit" class="rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold px-8 py-3 shadow-lg shadow-sky-600/30 transition">
        حفظ
    </button>
    <a href="{{ route('patients.index') }}" class="rounded-xl bg-slate-100 hover:bg-slate-200 font-bold px-8 py-3 transition">إلغاء</a>
</div>