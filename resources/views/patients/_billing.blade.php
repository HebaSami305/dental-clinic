@php
    $outstanding = $patient->invoices->sum(fn ($i) => max($i->balance, 0));
@endphp

<div class="p-5">
    <div class="flex items-center justify-between mb-5">
        <h3 class="font-extrabold text-slate-900">الفواتير والدفعات</h3>
        <div class="text-sm">
            المتبقي على المريض:
            <span class="font-extrabold {{ $outstanding > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                {{ number_format($outstanding, 2) }} ₪
            </span>
        </div>
    </div>

    <div class="border border-slate-200 rounded-2xl divide-y divide-slate-100 overflow-hidden">
        @forelse ($patient->invoices as $invoice)
            <a href="{{ route('invoices.show', $invoice) }}" class="flex flex-wrap items-center gap-4 px-5 py-4 hover:bg-slate-50">
                <div class="w-28 shrink-0">
                    <div class="font-bold text-sky-700">{{ $invoice->number }}</div>
                    <div class="text-xs text-slate-500">{{ $invoice->issued_at->format('Y-m-d') }}</div>
                </div>

                <div class="flex-1 text-sm text-slate-600">
                    الإجمالي {{ number_format($invoice->total, 2) }} ₪
                    · المدفوع {{ number_format($invoice->paid, 2) }} ₪
                </div>

                <div class="font-bold {{ $invoice->balance > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                    {{ number_format($invoice->balance, 2) }} ₪
                </div>

                <x-invoice-status :invoice="$invoice" />
            </a>
        @empty
            <div class="py-12 text-center text-slate-400">
                ما في فواتير. أنشئ فاتورة من تبويب الزيارات
            </div>
        @endforelse
    </div>
</div>