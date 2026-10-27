@php
    $config = [
        'teeth' => (object) $patient->toothRecords
            ->keyBy('tooth_number')
            ->map(fn ($r) => ['condition' => $r->condition, 'notes' => $r->notes])
            ->all(),
        'conditions' => \App\Models\ToothRecord::CONDITIONS,
        'url' => route('patients.teeth.update', [$patient, '__TOOTH__']),
    ];

    $arches = [
        'upper' => ['label' => 'الفك العلوي', 'teeth' => \App\Models\ToothRecord::UPPER],
        'lower' => ['label' => 'الفك السفلي', 'teeth' => \App\Models\ToothRecord::LOWER],
    ];
@endphp

<div class="p-5" x-data="odontogram(@js($config))">

    {{-- الدليل --}}
    <div class="flex flex-wrap gap-x-5 gap-y-2 mb-6 text-sm">
        <template x-for="(c, key) in conditions" :key="key">
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded border border-slate-400" :style="`background:${c.color}`"></span>
                <span class="text-slate-600" x-text="c.label"></span>
            </div>
        </template>
    </div>

    {{-- المخطط (ltr عشان ترتيب FDI يضل صح) --}}
    <div class="overflow-x-auto pb-2">
        <div dir="ltr" class="min-w-[680px] space-y-6">
            @foreach ($arches as $arch => $data)
                <div>
                    <div class="text-center text-xs font-bold text-slate-400 mb-2">{{ $data['label'] }}</div>
                    <div class="flex justify-center">
                        @foreach ($data['teeth'] as $n)
                            <button type="button" @click="select({{ $n }})"
                                    class="flex flex-col items-center gap-1 p-1 rounded-lg hover:bg-slate-50 transition {{ $loop->iteration === 8 ? 'mr-5' : '' }}"
                                    :class="selected === {{ $n }} ? 'ring-2 ring-sky-500 bg-sky-50' : ''">

                                @if ($arch === 'upper')
                                    <span class="text-[11px] font-bold text-slate-500">{{ $n }}</span>
                                @endif

                                <svg viewBox="0 0 24 30" class="w-8 h-10 {{ $arch === 'lower' ? 'rotate-180' : '' }}">
                                    <path d="M12 2c-3 0-5 1-6 2.5C4.5 6.5 5 9.5 5.5 12c.5 2.5 1 6 1.5 9 .4 2.2 1.2 5 2.5 5s1.4-3 2.5-3 1.2 3 2.5 3 2.1-2.8 2.5-5c.5-3 1-6.5 1.5-9 .5-2.5 1-5.5-.5-7.5C17 3 15 2 12 2z"
                                          :fill="cond({{ $n }}) === 'missing' ? 'none' : color({{ $n }})"
                                          :stroke-dasharray="cond({{ $n }}) === 'missing' ? '3 2' : ''"
                                          stroke="#64748b" stroke-width="1.5" stroke-linejoin="round"/>
                                </svg>

                                @if ($arch === 'lower')
                                    <span class="text-[11px] font-bold text-slate-500">{{ $n }}</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- لوحة تعديل السن --}}
    <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5">
        <template x-if="selected === null">
            <p class="text-slate-500 text-center py-4">اضغط على أي سن لتسجيل حالته</p>
        </template>

        <template x-if="selected !== null">
            <div>
                <h3 class="font-extrabold text-slate-900 mb-4">
                    السن رقم <span x-text="selected" class="text-sky-700"></span>
                </h3>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2">الحالة</label>
                        <select x-model="form.condition"
                                class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:ring-sky-500">
                            <template x-for="(c, key) in conditions" :key="key">
                                <option :value="key" x-text="c.label"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-2">ملاحظات</label>
                        <textarea x-model="form.notes" rows="2"
                                  class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:ring-sky-500"
                                  placeholder="مثال: تسوس عميق على السطح الماضغ"></textarea>
                    </div>
                </div>

                <p x-show="error" x-text="error" class="text-sm text-rose-600 mt-3"></p>

                <div class="flex items-center gap-3 mt-4">
                    <button type="button" @click="save()" :disabled="saving"
                            class="rounded-xl bg-sky-600 hover:bg-sky-700 disabled:opacity-60 text-white font-bold px-6 py-2.5 transition">
                        <span x-text="saving ? 'جاري الحفظ...' : 'حفظ'"></span>
                    </button>
                    <button type="button" @click="selected = null"
                            class="rounded-xl bg-white border border-slate-200 hover:bg-slate-100 font-bold px-6 py-2.5 transition">إغلاق</button>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('odontogram', (config) => ({
            teeth: config.teeth,
            conditions: config.conditions,
            url: config.url,
            selected: null,
            form: { condition: 'healthy', notes: '' },
            saving: false,
            error: null,

            cond(n) {
                return this.teeth[n]?.condition ?? 'healthy';
            },

            color(n) {
                return this.conditions[this.cond(n)].color;
            },

            select(n) {
                this.selected = n;
                this.error = null;
                this.form = {
                    condition: this.teeth[n]?.condition ?? 'healthy',
                    notes: this.teeth[n]?.notes ?? '',
                };
            },

            async save() {
                this.saving = true;
                this.error = null;

                try {
                    const res = await fetch(this.url.replace('__TOOTH__', this.selected), {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify(this.form),
                    });

                    if (!res.ok) throw new Error('فشل الحفظ');

                    const data = await res.json();

                    if (data.condition === 'healthy' && !data.notes) {
                        delete this.teeth[this.selected];
                    } else {
                        this.teeth[this.selected] = data;
                    }
                } catch (e) {
                    this.error = 'ما انحفظ التعديل، جرّب مرة ثانية';
                } finally {
                    this.saving = false;
                }
            },
        }));
    });
</script>