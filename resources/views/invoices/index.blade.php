<x-app-layout>
    <x-slot name="header">الفواتير والدفعات</x-slot>

    @if (session('success'))
        <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3 font-semibold">{{ session('success') }}</div>
    @endif

    {{-- ملخص --}}
    <div class="grid sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500 mb-1">إجمالي الفواتير</div>
            <div class="text-2xl font-extrabold text-slate-900">{{ number_format($billed, 2) }} ₪</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500 mb-1">المحصّل</div>
            <div class="text-2xl font-extrabold text-emerald-600">{{ number_format($collected, 2) }} ₪</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500 mb-1">المتبقي (ذمم)</div>
            <div class="text-2xl font-extrabold text-rose-600">{{ number_format($outstanding, 2) }} ₪</div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 p-5 border-b border-slate-100">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="ابحث برقم الفاتورة أو اسم المريض..."
                   class="flex-1 rounded-xl border-slate-300 px-4 py-2.5 focus:border-sky-500 focus:ring-sky-500">

            <select name="status" onchange="this.form.submit()"
                    class="rounded-xl border-slate-300 px-4 py-2.5 focus:border-sky-500 focus:ring-sky-500">
                <option value="">كل الحالات</option>
                @foreach (\App\Models\Invoice::STATUS_LABELS as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-slate-500 bg-slate-50">
                    <tr>
                        <th class="text-start font-semibold px-5 py-3">الفاتورة</th>
                        <th class="text-start font-semibold px-5 py-3">المريض</th>
                        <th class="text-start font-semibold px-5 py-3">التاريخ</th>
                        <th class="text-start font-semibold px-5 py-3">الإجمالي</th>
                        <th class="text-start font-semibold px-5 py-3">المدفوع</th>
                        <th class="text-start font-semibold px-5 py-3">المتبقي</th>
                        <th class="text-start font-semibold px-5 py-3">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($invoices as $invoice)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <a href="{{ route('invoices.show', $invoice) }}" class="font-bold text-sky-700 hover:text-sky-900">{{ $invoice->number }}</a>
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-900">{{ $invoice->patient->name }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $invoice->issued_at->format('Y-m-d') }}</td>
                            <td class="px-5 py-4">{{ number_format($invoice->total, 2) }}</td>
                            <td class="px-5 py-4 text-emerald-700">{{ number_format($invoice->paid, 2) }}</td>
                            <td class="px-5 py-4 font-bold {{ $invoice->balance > 0 ? 'text-rose-600' : 'text-slate-400' }}">{{ number_format($invoice->balance, 2) }}</td>
                            <td class="px-5 py-4"><x-invoice-status :invoice="$invoice" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-12 text-center text-slate-500">لا يوجد فواتير</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($invoices->hasPages())
            <div class="p-5 border-t border-slate-100">{{ $invoices->links() }}</div>
        @endif
    </div>
</x-app-layout>