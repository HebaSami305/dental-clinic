<x-app-layout>
    <x-slot name="header">مريض جديد</x-slot>

    <form method="POST" action="{{ route('patients.store') }}" class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 max-w-4xl">
        @csrf
        @include('patients._form')
    </form>
</x-app-layout>