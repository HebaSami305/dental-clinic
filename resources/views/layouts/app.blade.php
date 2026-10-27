<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>[x-cloak]{display:none !important}</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800" x-data="{ sidebar: false }">
<div class="min-h-screen lg:flex">

    {{-- خلفية معتمة للموبايل --}}
    <div x-show="sidebar" x-cloak @click="sidebar = false"
         class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden"></div>

    {{-- السايدبار --}}
    @include('layouts.sidebar')

    {{-- المحتوى --}}
    <div class="flex-1 min-w-0 flex flex-col">

        {{-- الشريط العلوي --}}
        <header class="sticky top-0 z-20 bg-white/80 backdrop-blur border-b border-slate-200">
            <div class="flex items-center justify-between h-16 px-4 sm:px-8">
                <div class="flex items-center gap-3">
                    <button @click="sidebar = true" class="lg:hidden p-2 rounded-lg hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/>
                        </svg>
                    </button>
                    @isset($header)
                        <h1 class="text-xl font-extrabold text-slate-900">{{ $header }}</h1>
                    @endisset
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-end hidden sm:block leading-tight">
                        <div class="text-sm font-bold text-slate-900">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-slate-500">{{ Auth::user()->email }}</div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center font-extrabold">
                        {{ mb_substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-8">
            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
