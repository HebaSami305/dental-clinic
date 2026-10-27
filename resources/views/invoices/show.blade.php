<x-app-layout>
    <x-slot name="header">فاتورة {{ $invoice->number }}</x-slot>

    @php
        $input = 'w-full rounded-xl border-slate-300 bg-white px-4 py-2.5 focus:border-sky-500 focus:ring-sky-500';
    @endphp

    @if (session('success'))
        <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3 font-semibold">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-5 py-3 text-sm">
            <ul class="list-disc ps-5 space-y-1">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- الفاتورة --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200">
                <div class="flex flex-wrap items-center justify-between gap-3 p-5 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-xl font-extrabold text-slate-900">{{ $invoice->number }}</h2>
                            <x-invoice-status :invoice="$invoice" />
                        </div>
                        <div class="text-sm text-slate-500 mt-1">
                            <a href="{{ route('patients.show', ['patient' => $invoice->patient_id, 'tab' => 'billing']) }}"
                               class="font-semibold text-sky-700 hover:text-sky-900">{{ $invoice->patient->name }}</a>
                            · {{ $invoice->issued_at->format('Y-m-d') }}
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" onclick="window.print()"
                                class="rounded-xl bg-slate-100 hover:bg-slate-200 font-bold px-4 py-2 text-sm transition">طباعة</button>

                        @if ($invoice->payments->isEmpty())
                            <form method="POST" action="{{ route('invoices.destroy', $invoice) }}"
                                  onsubmit="return confirm('متأكد من حذف الفاتورة؟')">
                                @csrf
                                @method('DELETE')
                                <button class="text-sm font-bold text-rose-600 hover:text-rose-800">حذف</button>
                            </form>
                        @endif
                    </div>
                </div>

                <table class="w-full text-sm">
                    <thead class="text-xs text-slate-500 bg-slate-50">
                        <tr>
                            <th class="text-start font-semibold px-5 py-3">البند</th>
                            <th class="text-start font-semibold px-5 py-3">السن</th>
                            <th class="text-end font-semibold px-5 py-3">السعر</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($invoice->items as $item)
                            <tr>
                                <td class="px-5 py-3 font-semibold text-slate-800">{{ $item->description }}</td>
                                <td class="px-5 py-3 text-slate-500">{{ $item->tooth_number ?: '—' }}</td>
                                <td class="px-5 py-3 text-end">{{ number_format($item->price, 2) }} ₪</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="p-5 border-t border-slate-100 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-slate-500">المجموع</span><span>{{ number_format($invoice->subtotal, 2) }} ₪</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">الخصم</span><span>- {{ number_format($invoice->discount, 2) }} ₪</span></div>
                    <div class="flex justify-between text-base font-extrabold text-slate-900 pt-2 border-t border-slate-100">
                        <span>الإجمالي</span><span>{{ number_format($invoice->total, 2) }} ₪</span>
                    </div>
                    <div class="flex justify-between text-emerald-700"><span>المدفوع</span><span>{{ number_format($invoice->paid, 2) }} ₪</span></div>
                    <div class="flex justify-between font-extrabold {{ $invoice->balance > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                        <span>المتبقي</span><span>{{ number_format($invoice->balance, 2) }} ₪</span>
                    </div>
                </div>
            </div>

            {{-- الخصم والملاحظات --}}
            <form method="POST" action="{{ route('invoices.update', $invoice) }}"
                  class="bg-white rounded-2xl border border-slate-200 p-5">
                @csrf
                @method('PATCH')
                <h3 class="font-extrabold text-slate-900 mb-4">الخصم والملاحظات</h3>

                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2">الخصم (₪)</label>
                        <input type="number" step="0.01" min="0" name="discount" dir="ltr"
                               value="{{ old('discount', $invoice->discount) }}" class="{{ $input }} text-left">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold mb-2">ملاحظات</label>
                        <input type="text" name="notes" value="{{ old('notes', $invoice->notes) }}" class="{{ $input }}">
                    </div>
                </div>

                <button class="mt-4 rounded-xl bg-slate-900 hover:bg-slate-700 text-white font-bold px-6 py-2.5 text-sm transition">حفظ</button>
            </form>
        </div>

        {{-- الدفعات --}}
        <div class="space-y-6">
            @if ($invoice->balance > 0)
                <form method="POST" action="{{ route('invoices.payments.store', $invoice) }}"
                      class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
                    @csrf
                    <h3 class="font-extrabold text-slate-900">تسجيل دفعة</h3>

                    <div>
                        <label class="block text-sm font-semibold mb-2">المبلغ (₪)</label>
                        <input type="number" step="0.01" min="0.01" max="{{ $invoice->balance }}" name="amount" dir="ltr" required
                               value="{{ old('amount', $invoice->balance) }}" class="{{ $input }} text-left">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-2">طريقة الدفع</label>
                        <select name="method" class="{{ $input }}">
                            @foreach (\App\Models\Payment::METHODS as $key => $label)
                                <option value="{{ $key }}" @selected(old('method') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-2">تاريخ الدفع</label>
                        <input type="date" name="paid_at" dir="ltr" required
                               value="{{ old('paid_at', now()->format('Y-m-d')) }}" class="{{ $input }}">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-2">ملاحظات</label>
                        <input type="text" name="notes" value="{{ old('notes') }}" class="{{ $input }}">
                    </div>

                    <button class="w-full rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 transition">تسجيل الدفعة</button>
                </form>
            @else
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold text-center p-5">
                    الفاتورة مسددة بالكامل ✓
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200">
                <h3 class="font-extrabold text-slate-900 p-5 border-b border-slate-100">سجل الدفعات</h3>

                <div class="divide-y divide-slate-100">
                    @forelse ($invoice->payments as $payment)
                        <div class="flex items-center justify-between gap-3 px-5 py-4">
                            <div>
                                <div class="font-extrabold text-slate-900">{{ number_format($payment->amount, 2) }} ₪</div>
                                <div class="text-xs text-slate-500">
                                    {{ \App\Models\Payment::METHODS[$payment->method] ?? $payment->method }}
                                    · {{ $payment->paid_at->format('Y-m-d') }}
                                </div>
                                @if ($payment->notes)
                                    <div class="text-xs text-slate-400">{{ $payment->notes }}</div>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('payments.destroy', $payment) }}"
                                  onsubmit="return confirm('حذف هذه الدفعة؟')">
                                @csrf
                                @method('DELETE')
                                <button class="text-sm font-bold text-rose-600 hover:text-rose-800">حذف</button>
                            </form>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-sm">ما في دفعات بعد</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>