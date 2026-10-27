<x-app-layout>
    <x-slot name="header">المواعيد</x-slot>

    @if (session('success'))
        <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @php
        $badge = [
            'scheduled'   => 'bg-amber-50 text-amber-700',
            'in_progress' => 'bg-sky-50 text-sky-700',
            'completed'   => 'bg-emerald-50 text-emerald-700',
            'cancelled'   => 'bg-rose-50 text-rose-700',
            'no_show'     => 'bg-slate-100 text-slate-600',
        ];
    @endphp

    <div class="bg-white rounded-2xl border border-slate-200">

        {{-- التنقل بين الأيام --}}
        <div class="flex flex-col lg:flex-row gap-4 lg:items-center lg:justify-between p-5 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <a href="{{ route('appointments.index', ['date' => $date->copy()->subDay()->toDateString()]) }}"
                   class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center font-bold">›</a>

                <div class="min-w-48 text-center">
                    <div class="font-extrabold text-slate-900">{{ $date->translatedFormat('l') }}</div>
                    <div class="text-sm text-slate-500">{{ $date->format('Y-m-d') }}</div>
                </div>

                <a href="{{ route('appointments.index', ['date' => $date->copy()->addDay()->toDateString()]) }}"
                   class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center font-bold">‹</a>

                <a href="{{ route('appointments.index') }}"
                   class="text-sm font-bold text-sky-600 hover:text-sky-800 ms-2">اليوم</a>

                <form method="GET">
                    <input type="date" name="date" value="{{ $date->toDateString() }}" dir="ltr"
                           onchange="this.form.submit()"
                           class="rounded-xl border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
                </form>
            </div>

            <a href="{{ route('appointments.create') }}"
               class="rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold px-5 py-2.5 text-center transition">
                + حجز موعد
            </a>
        </div>

        {{-- قائمة المواعيد --}}
        <div class="divide-y divide-slate-100">
            @forelse ($appointments as $a)
                <div class="flex flex-col md:flex-row md:items-center gap-4 p-5 hover:bg-slate-50 {{ $a->status === 'cancelled' ? 'opacity-60' : '' }}">

                    <div class="md:w-32 shrink-0">
                        <div class="text-xl font-extrabold text-slate-900" dir="ltr" style="text-align:start">
                            {{ $a->starts_at->format('H:i') }}
                        </div>
                        <div class="text-xs text-slate-500">حتى {{ $a->ends_at->format('H:i') }} ({{ $a->duration_minutes }} د)</div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <a href="{{ route('patients.show', $a->patient) }}"
                           class="font-bold text-slate-900 hover:text-sky-700">{{ $a->patient->name }}</a>
                        <div class="text-sm text-slate-500">
                            {{ $a->type }}
                            @if ($a->notes) · {{ \Illuminate\Support\Str::limit($a->notes, 50) }} @endif
                        </div>
                    </div>

                    <form method="POST" action="{{ route('appointments.status', $a) }}">
                        @csrf
                        @method('PATCH')
                        <select name="status" onchange="this.form.submit()"
                                class="rounded-full border-0 text-xs font-bold ps-4 pe-8 py-2 {{ $badge[$a->status] }}">
                            @foreach (\App\Models\Appointment::STATUSES as $key => $label)
                                <option value="{{ $key }}" @selected($a->status === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>

                    <div class="flex items-center gap-4">
                        <a href="{{ route('appointments.edit', $a) }}" class="text-sm font-bold text-sky-600 hover:text-sky-800">تعديل</a>
                        <form method="POST" action="{{ route('appointments.destroy', $a) }}"
                              onsubmit="return confirm('متأكد من حذف هذا الموعد؟')">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm font-bold text-rose-600 hover:text-rose-800">حذف</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-16 text-center text-slate-500">لا يوجد مواعيد بهاد اليوم</div>
            @endforelse
        </div>
    </div>
</x-app-layout>