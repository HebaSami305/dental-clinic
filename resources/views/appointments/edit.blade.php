<x-app-layout>
    <x-slot name="header">تعديل الموعد</x-slot>

    <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 max-w-4xl">
        @csrf
        @method('PUT')
        @include('appointments._form')
    </form>
</x-app-layout>