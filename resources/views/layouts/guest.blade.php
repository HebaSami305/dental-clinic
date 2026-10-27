<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800">
    <div class="min-h-screen grid lg:grid-cols-2">

        {{-- جهة الفورم --}}
        <div class="flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md">
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-12 h-12 rounded-2xl bg-sky-600 text-white flex items-center justify-center shadow-lg shadow-sky-600/30">
                        <x-application-logo class="w-7 h-7" />
                    </div>
                    <span class="text-xl font-extrabold text-slate-900">{{ config('app.name') }}</span>
                </div>

                {{ $slot }}
            </div>
        </div>

        {{-- جهة الهوية --}}
        <div class="hidden lg:flex relative overflow-hidden bg-gradient-to-br from-sky-600 via-sky-700 to-teal-600 text-white items-center justify-center p-12">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-32 -right-16 w-[28rem] h-[28rem] rounded-full bg-white/5"></div>

            <div class="relative max-w-md">
                <x-application-logo class="w-16 h-16 mb-8 opacity-90" />
                <h2 class="text-4xl font-extrabold leading-snug mb-4">إدارة عيادتك<br>بكل سهولة واحترافية</h2>
                <p class="text-sky-100 leading-relaxed mb-10">
                    ملفات المرضى، المواعيد، مخطط الأسنان، والفواتير... كل شي بمكان واحد.
                </p>

                <ul class="space-y-3 text-sky-50">
                    <li class="flex items-center gap-3"><span class="w-2 h-2 rounded-full bg-teal-300"></span> جدولة مواعيد ذكية بدون تعارض</li>
                    <li class="flex items-center gap-3"><span class="w-2 h-2 rounded-full bg-teal-300"></span> مخطط أسنان تفاعلي لكل مريض</li>
                    <li class="flex items-center gap-3"><span class="w-2 h-2 rounded-full bg-teal-300"></span> متابعة الدفعات والأرصدة</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
