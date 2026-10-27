@php
    $badge = [
        'scheduled'   => 'bg-amber-50 text-amber-700',
        'in_progress' => 'bg-sky-50 text-sky-700',
        'completed'   => 'bg-emerald-50 text-emerald-700',
        'cancelled'   => 'bg-rose-50 text-rose-700',
        'no_show'     => 'bg-slate-100 text-slate-600',
    ];

    $appointments = $patient->appointments->sortByDesc('starts_at');
@endphp

<div class="p-5">
    <div class="flex items-center justify-between mb-5">
        <h3 class="font-extrabold text-slate-900">مواعيد المريض</h3>
        <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}"
           class="rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-2 text-sm transition">+ حجز موعد</a>
    </div>

    <div class="border border-slate-200 rounded-2xl divide-y divide-slate-100 overflow-hidden">
        @forelse ($appointments as $a)
            <div class="flex flex-wrap items-center gap-4 px-5 py-4 {{ $a->status === 'cancelled' ? 'opacity-60' : '' }}">
                <div class="w-32 shrink-0">
                    <div class="font-extrabold text-slate-900">{{ $a->starts_at->format('Y-m-d') }}</div>
                    <div class="text-xs text-slate-500" dir="ltr" style="text-align:start">
                        {{ $a->starts_at->format('H:i') }} - {{ $a->ends_at->format('H:i') }}
                    </div>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-slate-800">{{ $a->type }}</div>
                    @if ($a->notes)
                        <div class="text-sm text-slate-500">{{ \Illuminate\Support\Str::limit($a->notes, 60) }}</div>
                    @endif
                </div>

                <span class="text-xs font-bold rounded-full px-3 py-1 {{ $badge[$a->status] ?? 'bg-slate-100 text-slate-600' }}">
                    {{ \App\Models\Appointment::STATUSES[$a->status] ?? $a->status }}
                </span>

                <a href="{{ route('appointments.edit', $a) }}" class="text-sm font-bold text-sky-600 hover:text-sky-800">تعديل</a>
            </div>
        @empty
            <div class="py-12 text-center text-slate-400">ما في مواعيد لهالمريض بعد</div>
        @endforelse
    </div>
</div>