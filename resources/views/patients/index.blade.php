<x-app-layout>
    <x-slot name="header">المرضى</x-slot>

    @if (session('success'))
        <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200">

        {{-- البحث والإضافة --}}
        <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between p-5 border-b border-slate-100">
            <form method="GET" class="flex-1 max-w-md">
                <input type="search" name="q" value="{{ request('q') }}"
                       placeholder="ابحث بالاسم أو الهاتف أو رقم الملف..."
                       class="w-full rounded-xl border-slate-300 px-4 py-2.5 focus:border-sky-500 focus:ring-sky-500">
            </form>

            <a href="{{ route('patients.create') }}"
               class="rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold px-5 py-2.5 text-center transition">
                + مريض جديد
            </a>
        </div>

        {{-- الجدول --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="text-xs text-slate-500 bg-slate-50">
                    <tr>
                        <th class="text-start font-semibold px-5 py-3">رقم الملف</th>
                        <th class="text-start font-semibold px-5 py-3">الاسم</th>
                        <th class="text-start font-semibold px-5 py-3">الهاتف</th>
                        <th class="text-start font-semibold px-5 py-3">العمر</th>
                        <th class="text-start font-semibold px-5 py-3">الجنس</th>
                        <th class="text-start font-semibold px-5 py-3">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($patients as $patient)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-bold text-sky-700">{{ $patient->file_number }}</td>
                            
                            <td class="px-5 py-4 font-semibold">
    <a href="{{ route('patients.show', $patient) }}" class="text-slate-900 hover:text-sky-700">
        {{ $patient->name }}
    </a>
</td>
                            <td class="px-5 py-4 text-slate-600" dir="ltr" style="text-align:right">{{ $patient->phone }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $patient->age ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $patient->gender === 'male' ? 'ذكر' : 'أنثى' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('patients.edit', $patient) }}"
                                       class="text-sm font-bold text-sky-600 hover:text-sky-800">تعديل</a>

                                    <form method="POST" action="{{ route('patients.destroy', $patient) }}"
                                          onsubmit="return confirm('متأكد من حذف هذا المريض؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-sm font-bold text-rose-600 hover:text-rose-800">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                                لا يوجد مرضى مطابقين
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($patients->hasPages())
            <div class="p-5 border-t border-slate-100">
                {{ $patients->links() }}
            </div>
        @endif
    </div>
</x-app-layout>