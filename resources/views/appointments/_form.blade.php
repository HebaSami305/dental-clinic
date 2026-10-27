@php
    $input = 'w-full rounded-xl border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:ring-sky-500';
    $currentPatient = old('patient_id', $appointment->patient_id ?? ($selectedPatient ?? ''));
    $currentType = old('type', $appointment->type ?? '');
@endphp

<div class="grid sm:grid-cols-2 gap-5">
    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold mb-2">المريض *</label>
        <select name="patient_id" required class="{{ $input }}">
            <option value="">اختر المريض...</option>
            @foreach ($patients as $p)
                <option value="{{ $p->id }}" @selected($currentPatient == $p->id)>
                    {{ $p->name }} ({{ $p->file_number }})
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('patient_id')" class="mt-2" />
    </div>

    <div>
        <label class="block text-sm font-semibold mb-2">التاريخ والوقت *</label>
        <input type="datetime-local" name="starts_at" dir="ltr" required class="{{ $input }}"
               value="{{ old('starts_at', optional($appointment->starts_at ?? null)->format('Y-m-d\TH:i')) }}">
        <x-input-error :messages="$errors->get('starts_at')" class="mt-2" />
    </div>

    <div>
        <label class="block text-sm font-semibold mb-2">المدة *</label>
        <select name="duration_minutes" class="{{ $input }}">
            @foreach ([15, 30, 45, 60, 90, 120] as $m)
                <option value="{{ $m }}" @selected(old('duration_minutes', $appointment->duration_minutes ?? 30) == $m)>{{ $m }} دقيقة</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('duration_minutes')" class="mt-2" />
    </div>

    <div>
        <label class="block text-sm font-semibold mb-2">نوع الإجراء *</label>
        <select name="type" required class="{{ $input }}">
            <option value="">اختر...</option>
            @foreach (\App\Models\Appointment::TYPES as $t)
                <option value="{{ $t }}" @selected($currentType === $t)>{{ $t }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('type')" class="mt-2" />
    </div>

    @isset($appointment)
        <div>
            <label class="block text-sm font-semibold mb-2">الحالة *</label>
            <select name="status" class="{{ $input }}">
                @foreach (\App\Models\Appointment::STATUSES as $key => $label)
                    <option value="{{ $key }}" @selected(old('status', $appointment->status) === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
        </div>
    @endisset

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold mb-2">ملاحظات</label>
        <textarea name="notes" rows="3" class="{{ $input }}">{{ old('notes', $appointment->notes ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
    </div>
</div>

<div class="flex items-center gap-3 mt-8">
    <button type="submit" class="rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold px-8 py-3 shadow-lg shadow-sky-600/30 transition">حفظ</button>
    <a href="{{ route('appointments.index') }}" class="rounded-xl bg-slate-100 hover:bg-slate-200 font-bold px-8 py-3 transition">إلغاء</a>
</div>