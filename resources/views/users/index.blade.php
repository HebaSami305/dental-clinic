<x-app-layout>
    <x-slot name="header">المستخدمون</x-slot>

    @if (session('success'))
        <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3 font-semibold">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-5 py-3 text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200">
        <div class="flex items-center justify-between p-5 border-b border-slate-100">
            <h2 class="font-extrabold text-slate-900">مستخدمو النظام</h2>
            <a href="{{ route('users.create') }}"
               class="rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold px-5 py-2.5 text-sm transition">+ مستخدم جديد</a>
        </div>

        <table class="w-full text-sm">
            <thead class="text-xs text-slate-500 bg-slate-50">
                <tr>
                    <th class="text-start font-semibold px-5 py-3">الاسم</th>
                    <th class="text-start font-semibold px-5 py-3">البريد</th>
                    <th class="text-start font-semibold px-5 py-3">الدور</th>
                    <th class="text-start font-semibold px-5 py-3">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($users as $user)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4 font-semibold text-slate-900">
                            {{ $user->name }}
                            @if ($user->id === auth()->id()) <span class="text-xs text-slate-400">(أنت)</span> @endif
                        </td>
                        <td class="px-5 py-4 text-slate-600" dir="ltr" style="text-align:right">{{ $user->email }}</td>
                        <td class="px-5 py-4">
                            <span class="text-xs font-bold bg-sky-50 text-sky-700 rounded-full px-3 py-1">{{ $user->role_label }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-4">
                                <a href="{{ route('users.edit', $user) }}" class="font-bold text-sky-600 hover:text-sky-800">تعديل</a>
                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $user) }}"
                                          onsubmit="return confirm('حذف هذا المستخدم؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="font-bold text-rose-600 hover:text-rose-800">حذف</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>