<x-app-layout>
    <x-slot name="header">لوحة التحكم</x-slot>

    @php
        $icons = [
            'calendar' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5',
            'users' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
            'card' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z',
        ];

        $cards = [
            ['label' => 'مواعيد اليوم', 'value' => $stats['appointments_today'], 'icon' => 'calendar', 'box' => 'bg-sky-50 text-sky-600'],
            ['label' => 'إجمالي المرضى', 'value' => $stats['patients_total'], 'icon' => 'users', 'box' => 'bg-teal-50 text-teal-600'],
            ['label' => 'مرضى جدد هذا الشهر', 'value' => $stats['patients_month'], 'icon' => 'users', 'box' => 'bg-violet-50 text-violet-600'],
            ['label' => 'إيراد اليوم', 'value' => number_format($stats['revenue_today'], 2) . ' ₪', 'icon' => 'card', 'box' => 'bg-amber-50 text-amber-600'],
            ['label' => 'إيراد هذا الشهر', 'value' => number_format($stats['revenue_month'], 2) . ' ₪', 'icon' => 'card', 'box' => 'bg-emerald-50 text-emerald-600'],
            ['label' => 'ذمم غير محصّلة', 'value' => number_format($stats['outstanding'], 2) . ' ₪', 'icon' => 'card', 'box' => 'bg-rose-50 text-rose-600'],
        ];

        $badge = [
            'scheduled'   => 'bg-amber-50 text-amber-700',
            'in_progress' => 'bg-sky-50 text-sky-700',
            'completed'   => 'bg-emerald-50 text-emerald-700',
            'cancelled'   => 'bg-rose-50 text-rose-700',
            'no_show'     => 'bg-slate-100 text-slate-600',
        ];
    @endphp

    {{-- البطاقات --}}
    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-8">
        @foreach ($cards as $c)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 {{ $c['box'] }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$c['icon']] }}"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $c['value'] }}</div>
                    <div class="text-sm text-slate-500">{{ $c['label'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid xl:grid-cols-3 gap-6">

        <div class="xl:col-span-2 space-y-6">

            {{-- مواعيد اليوم --}}
            <div class="bg-white rounded-2xl border border-slate-200">
                <div class="flex items-center justify-between p-5 border-b border-slate-100">
                    <h2 class="font-extrabold text-slate-900">مواعيد اليوم</h2>
                    <a href="{{ route('appointments.index') }}" class="text-sm font-semibold text-sky-600 hover:text-sky-800">عرض الكل</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="text-xs text-slate-500 bg-slate-50">
                            <tr>
                                <th class="text-start font-semibold px-5 py-3">الوقت</th>
                                <th class="text-start font-semibold px-5 py-3">المريض</th>
                                <th class="text-start font-semibold px-5 py-3">الإجراء</th>
                                <th class="text-start font-semibold px-5 py-3">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($appointments as $a)
                                <tr class="hover:bg-slate-50 {{ $a->status === 'cancelled' ? 'opacity-60' : '' }}">
                                    <td class="px-5 py-4 font-bold text-slate-900">{{ $a->starts_at->format('H:i') }}</td>
                                    <td class="px-5 py-4">
                                        <a href="{{ route('patients.show', $a->patient) }}" class="hover:text-sky-700">{{ $a->patient->name }}</a>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">{{ $a->type }}</td>
                                    <td class="px-5 py-4">
                                        <span class="text-xs font-bold rounded-full px-3 py-1 {{ $badge[$a->status] }}">
                                            {{ \App\Models\Appointment::STATUSES[$a->status] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-12 text-center text-slate-500">لا يوجد مواعيد اليوم</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- إيراد آخر 7 أيام --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h2 class="font-extrabold text-slate-900 mb-6">الإيراد آخر 7 أيام</h2>

                <div class="flex items-end gap-3 h-44">
                    @foreach ($chart as $d)
                        <div class="flex-1 flex flex-col items-center justify-end gap-2">
                            <div class="text-[11px] font-semibold text-slate-500 h-4">
                                {{ $d['total'] > 0 ? number_format($d['total']) : '' }}
                            </div>
                            <div class="w-full rounded-t-lg bg-sky-500"
                                 style="height: {{ max(round($d['total'] / $chartMax * 100), 3) }}px"></div>
                            <div class="text-xs text-slate-500">{{ $d['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- إجراءات سريعة --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 self-start">
            <h2 class="font-extrabold text-slate-900 mb-4">إجراءات سريعة</h2>
            <div class="space-y-3">
                <a href="{{ route('patients.create') }}" class="block rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-center py-3 transition">+ مريض جديد</a>
                <a href="{{ route('appointments.create') }}" class="block rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-center py-3 transition">+ حجز موعد</a>
                <a href="{{ route('invoices.index') }}" class="block rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-center py-3 transition">الفواتير والدفعات</a>
            </div>
        </div>
    </div>
</x-app-layout>
