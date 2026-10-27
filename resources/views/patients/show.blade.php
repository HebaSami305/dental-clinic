<x-app-layout>
    <x-slot name="header">ملف المريض</x-slot>

    @if (session('success'))
        <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    {{-- بطاقة المريض --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="w-20 h-20 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center text-3xl font-extrabold">
                {{ mb_substr($patient->name, 0, 1) }}
            </div>

            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-2xl font-extrabold text-slate-900">{{ $patient->name }}</h2>
                    <span class="text-xs font-bold bg-sky-50 text-sky-700 rounded-full px-3 py-1">{{ $patient->file_number }}</span>
                </div>

                <div class="flex flex-wrap gap-x-6 gap-y-1 mt-2 text-sm text-slate-600">
                    <span>{{ $patient->gender === 'male' ? 'ذكر' : 'أنثى' }}</span>
                    @if ($patient->age) <span>{{ $patient->age }} سنة</span> @endif
                    <span dir="ltr">{{ $patient->phone }}</span>
                    @if ($patient->address) <span>{{ $patient->address }}</span> @endif
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('patients.edit', $patient) }}"
                   class="rounded-xl bg-slate-100 hover:bg-slate-200 font-bold px-5 py-2.5 transition">تعديل</a>
                <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}"
                   class="rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-2.5 transition">حجز موعد</a>
                <a href="{{ route('patients.index') }}"
                   class="rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold px-5 py-2.5 transition">القائمة</a>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- المعلومات الطبية --}}
        <div class="space-y-6">
            <div class="rounded-2xl border p-5 {{ $patient->allergies ? 'bg-rose-50 border-rose-200' : 'bg-white border-slate-200' }}">
                <h3 class="font-extrabold mb-2 {{ $patient->allergies ? 'text-rose-700' : 'text-slate-900' }}">
                    {{ $patient->allergies ? '⚠️ تنبيه: حساسية' : 'الحساسية' }}
                </h3>
                <p class="{{ $patient->allergies ? 'text-rose-800 font-semibold' : 'text-slate-500' }}">
                    {{ $patient->allergies ?: 'لا يوجد' }}
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h3 class="font-extrabold text-slate-900 mb-2">التاريخ المرضي</h3>
                <p class="text-slate-600">{{ $patient->medical_history ?: 'لا يوجد' }}</p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5 text-sm space-y-2">
                <div class="flex justify-between"><span class="text-slate-500">تاريخ الميلاد</span>
                    <span class="font-semibold">{{ $patient->birth_date?->format('Y-m-d') ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">تاريخ التسجيل</span>
                    <span class="font-semibold">{{ $patient->created_at->format('Y-m-d') }}</span></div>
            </div>
        </div>
             {{-- التبويبات --}}
        @php
            $tabs = collect([
                'visits'       => ['label' => 'الزيارات',      'can' => 'visits.view'],
                'chart'        => ['label' => 'مخطط الأسنان', 'can' => 'chart'],
                'appointments' => ['label' => 'المواعيد',      'can' => 'appointments'],
                'billing'      => ['label' => 'الفواتير',      'can' => 'billing'],
            ])->filter(fn ($t) => auth()->user()->can($t['can']));
        @endphp

        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200"
             x-data="{ tab: (t => @js($tabs->keys()).includes(t) ? t : @js($tabs->keys()->first()))(new URLSearchParams(location.search).get('tab')) }">

            @if ($tabs->isEmpty())
                <div class="p-10 text-center text-slate-400">ما في معلومات متاحة لدورك</div>
            @else
                <div class="flex gap-1 p-2 border-b border-slate-100 overflow-x-auto">
                    @foreach ($tabs as $key => $t)
                        <button type="button" @click="tab = '{{ $key }}'"
                                :class="tab === '{{ $key }}' ? 'bg-sky-50 text-sky-700' : 'text-slate-500 hover:bg-slate-50'"
                                class="px-4 py-2 rounded-xl font-bold text-sm whitespace-nowrap transition">
                            {{ $t['label'] }}
                        </button>
                    @endforeach
                </div>

                @can('visits.view')
                    <div x-show="tab === 'visits'" x-cloak>@include('patients._visits')</div>
                @endcan
                @can('chart')
                    <div x-show="tab === 'chart'" x-cloak>@include('patients._odontogram')</div>
                @endcan
                @can('appointments')
                    <div x-show="tab === 'appointments'" x-cloak>@include('patients._appointments')</div>
                @endcan
                @can('billing')
                    <div x-show="tab === 'billing'" x-cloak>@include('patients._billing')</div>
                @endcan
            @endif
        </div>
        </div>
    </div>
</x-app-layout>