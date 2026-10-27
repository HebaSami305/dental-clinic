<x-app-layout>
    <x-slot name="header">حجز موعد</x-slot>

    <form method="POST" action="{{ route('appointments.store') }}" class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 max-w-4xl">
        @csrf
        @include('appointments._form')
    </form>
</x-app-layout>