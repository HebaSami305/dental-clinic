<x-app-layout>
    <x-slot name="header">تعديل الزيارة</x-slot>

    @php($patient = $visit->patient)

    <form method="POST" action="{{ route('visits.update', $visit) }}"
          class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 max-w-4xl">
        @csrf
        @method('PUT')
        @include('visits._form')
    </form>
</x-app-layout>