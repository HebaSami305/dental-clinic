@php
    $input = 'w-full rounded-xl border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:ring-sky-500';

    $rows = old('procedures', isset($visit)
        ? $visit->procedures->map(fn ($p) => [
            'name' => $p->name,
            'tooth_number' => $p->tooth_number,
            'price' => $p->price,
        ])->values()->all()
        : [['name' => '', 'tooth_number' => '', 'price' => '']]);

    $catalog = collect(\App\Models\VisitProcedure::CATALOG)->map(fn ($c) => $c['price'])->all();

    $teeth = \App\Models\ToothRecord::allTeeth();
    sort($teeth);
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-5 py-3 text-sm">
        <ul class="list-disc ps-5 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div x-data="visitForm(@js($rows), @js($catalog))">

    <div class="grid sm:grid-cols-2 gap-5 mb-6">
        <div>
            <label class="block text-sm font-semibold mb-2">تاريخ الزيارة *</label>
            <input type="date" name="visited_at" dir="ltr" required class="{{ $input }}"
                   value="{{ old('visited_at', optional($visit->visited_at ?? null)->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2">التشخيص</label>
            <input type="text" name="diagnosis" class="{{ $input }}"
                   value="{{ old('diagnosis', $visit->diagnosis ?? '') }}" placeholder="مثال: تسوس عميق بالسن 36">
        </div>

        <div class="sm:col-span-2">
            <label class="block text-sm font-semibold mb-2">ملاحظات الطبيب</label>
            <textarea name="notes" rows="2" class="{{ $input }}">{{ old('notes', $visit->notes ?? '') }}</textarea>
        </div>
    </div>

    {{-- الإجراءات --}}
    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-extrabold text-slate-900">الإجراءات</h3>
            <button type="button" @click="add()"
                    class="text-sm font-bold text-sky-600 hover:text-sky-800">+ إضافة إجراء</button>
        </div>

        <div class="space-y-3">
            <template x-for="(row, i) in rows" :key="row.key">
                <div class="grid grid-cols-12 gap-3 items-end bg-white rounded-xl border border-slate-200 p-3">

                    <div class="col-span-12 sm:col-span-5">
                        <label class="block text-xs font-semibold text-slate-500 mb-1">الإجراء</label>
                        <select x-model="row.name" @change="pick(i)" :name="`procedures[${i}][name]`" required class="{{ $input }}">
                            <option value="">اختر...</option>
                            @foreach (array_keys(\App\Models\VisitProcedure::CATALOG) as $name)
                                <option value="{{ $name }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <label class="block text-xs font-semibold text-slate-500 mb-1">رقم السن</label>
                        <select x-model="row.tooth_number" :name="`procedures[${i}][tooth_number]`" class="{{ $input }}">
                            <option value="">—</option>
                            @foreach ($teeth as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-5 sm:col-span-3">
                        <label class="block text-xs font-semibold text-slate-500 mb-1">السعر (₪)</label>
                        <input type="number" step="0.01" min="0" dir="ltr" x-model="row.price"
                               :name="`procedures[${i}][price]`" required class="{{ $input }} text-left">
                    </div>

                    <div class="col-span-1 flex justify-center pb-3">
                        <button type="button" @click="remove(i)" x-show="rows.length > 1"
                                class="text-rose-500 hover:text-rose-700 font-bold text-xl leading-none" title="حذف">×</button>
                    </div>
                </div>
            </template>
        </div>

        <div class="flex items-center justify-between mt-5 pt-4 border-t border-slate-200">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="update_chart" value="1" checked
                       class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                <span class="text-sm text-slate-600">تحديث مخطط الأسنان تلقائياً</span>
            </label>

            <div class="font-extrabold text-slate-900">
                الإجمالي: <span x-text="total.toFixed(2)"></span> ₪
            </div>
        </div>
    </div>
</div>

<div class="flex items-center gap-3 mt-8">
    <button type="submit" class="rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold px-8 py-3 shadow-lg shadow-sky-600/30 transition">حفظ الزيارة</button>
    <a href="{{ route('patients.show', ['patient' => $patient->id, 'tab' => 'visits']) }}"
       class="rounded-xl bg-slate-100 hover:bg-slate-200 font-bold px-8 py-3 transition">إلغاء</a>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('visitForm', (rows, catalog) => ({
            catalog,
            rows: rows.map(r => ({
                name: r.name ?? '',
                tooth_number: r.tooth_number ?? '',
                price: r.price ?? '',
                key: Math.random(),
            })),

            add() {
                this.rows.push({ name: '', tooth_number: '', price: '', key: Math.random() });
            },

            remove(i) {
                if (this.rows.length > 1) this.rows.splice(i, 1);
            },

            pick(i) {
                const price = this.catalog[this.rows[i].name];
                if (price !== undefined) this.rows[i].price = price;
            },

            get total() {
                return this.rows.reduce((sum, r) => sum + (parseFloat(r.price) || 0), 0);
            },
        }));
    });
</script>