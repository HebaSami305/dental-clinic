<x-app-layout>
    <x-slot name="header">غير مصرّح</x-slot>

    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center max-w-xl mx-auto">
        <div class="text-6xl mb-4">🔒</div>
        <h2 class="text-2xl font-extrabold text-slate-900 mb-2">ما عندك صلاحية لهالصفحة</h2>
        <p class="text-slate-500 mb-6">تواصل مع مدير النظام إذا كنت بحاجة للوصول.</p>
        <a href="{{ route('dashboard') }}" class="rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold px-6 py-3 transition">العودة للوحة التحكم</a>
    </div>
</x-app-layout>