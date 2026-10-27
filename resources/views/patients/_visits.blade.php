<div class="p-5">
    <div class="flex items-center justify-between mb-5">
        <h3 class="font-extrabold text-slate-900">سجل الزيارات</h3>
        <a href="{{ route('patients.visits.create', $patient) }}"
           class="rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold px-5 py-2 text-sm transition">+ زيارة جديدة</a>
    </div>

    @forelse ($patient->visits as $visit)
        <div class="border border-slate-200 rounded-2xl mb-4 overflow-hidden">

            <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50 px-5 py-3">
                <div>
                    <div class="font-extrabold text-slate-900">{{ $visit->visited_at->format('Y-m-d') }}</div>
                    @if ($visit->diagnosis)
                        <div class="text-sm text-slate-600">{{ $visit->diagnosis }}</div>
                    @endif
                </div>

            <div class="flex items-center gap-4">
    <span class="font-extrabold text-sky-700">{{ number_format($visit->total, 2) }} ₪</span>

    @if ($visit->invoice)
        @can('billing')
            <a href="{{ route('invoices.show', $visit->invoice) }}"
               class="text-sm font-bold text-emerald-600 hover:text-emerald-800">فاتورة {{ $visit->invoice->number }}</a>
        @endcan
    @else
        @can('billing')
            <form method="POST" action="{{ route('visits.invoice.store', $visit) }}">
                @csrf
                <button class="text-sm font-bold text-emerald-600 hover:text-emerald-800">إنشاء فاتورة</button>
            </form>
        @endcan

        @can('visits.manage')
            <a href="{{ route('visits.edit', $visit) }}" class="text-sm font-bold text-sky-600 hover:text-sky-800">تعديل</a>
            <form method="POST" action="{{ route('visits.destroy', $visit) }}"
                  onsubmit="return confirm('متأكد من حذف هذه الزيارة؟')">
                @csrf
                @method('DELETE')
                <button class="text-sm font-bold text-rose-600 hover:text-rose-800">حذف</button>
            </form>
        @endcan
    @endif
</div>
           
            </div>

            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                    @foreach ($visit->procedures as $p)
                        <tr>
                            <td class="px-5 py-3 font-semibold text-slate-800">{{ $p->name }}</td>
                            <td class="px-5 py-3 text-slate-500">
                                {{ $p->tooth_number ? 'سن ' . $p->tooth_number : '—' }}
                            </td>
                            <td class="px-5 py-3 text-slate-700 text-end">{{ number_format($p->price, 2) }} ₪</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($visit->notes)
                <div class="px-5 py-3 border-t border-slate-100 text-sm text-slate-500">
                    <span class="font-semibold text-slate-600">ملاحظات:</span> {{ $visit->notes }}
                </div>
            @endif
        </div>
    @empty
        <div class="py-12 text-center text-slate-400">ما في زيارات مسجلة بعد</div>
    @endforelse
</div>