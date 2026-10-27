<x-app-layout>
    <x-slot name="header">زيارة جديدة: {{ $patient->name }}</x-slot>

    <form method="POST" action="{{ route('patients.visits.store', $patient) }}"
          class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 max-w-4xl">
        @csrf
        @include('visits._form')
    </form>
</x-app-layout>