<x-app-layout>
    <x-slot name="header">تعديل بيانات المريض</x-slot>

    <form method="POST" action="{{ route('patients.update', $patient) }}" class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 max-w-4xl">
        @csrf
        @method('PUT')
        @include('patients._form')
    </form>
</x-app-layout>