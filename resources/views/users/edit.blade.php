<x-app-layout>
    <x-slot name="header">تعديل مستخدم</x-slot>

    <form method="POST" action="{{ route('users.update', $user) }}" class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 max-w-3xl">
        @csrf
        @method('PUT')
        @include('users._form')
    </form>
</x-app-layout>