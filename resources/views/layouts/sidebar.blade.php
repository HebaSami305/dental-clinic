@php
    $items = [
        ['label' => 'لوحة التحكم', 'route' => 'dashboard', 'pattern' => 'dashboard',
         'icon' => 'M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
        ['label' => 'المرضى', 'route' => 'patients.index', 'pattern' => 'patients.*',
         'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
        ['label' => 'المواعيد', 'route' => 'appointments.index', 'pattern' => 'appointments.*',
         'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5'],
        ['label' => 'الفواتير والدفعات', 'route' => 'invoices.index', 'pattern' => 'invoices.*',
         'icon' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z'],
         ['label' => 'المستخدمون', 'route' => 'users.index', 'pattern' => 'users.*', 'can' => 'users',
 'icon' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z'],
    ];
@endphp

<aside class="fixed inset-y-0 right-0 z-40 w-72 bg-white border-l border-slate-200 flex flex-col
              transform transition-transform duration-200
              lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 lg:z-auto"
       :class="sidebar ? 'translate-x-0' : 'translate-x-full'">

    {{-- الشعار --}}
    <div class="flex items-center gap-3 h-16 px-6 border-b border-slate-100">
        <div class="w-10 h-10 rounded-xl bg-sky-600 text-white flex items-center justify-center shadow-md shadow-sky-600/30">
            <x-application-logo class="w-6 h-6" />
        </div>
        <span class="text-lg font-extrabold text-slate-900">{{ config('app.name') }}</span>
    </div>

    {{-- القائمة --}}
    <nav class="flex-1 overflow-y-auto p-4 space-y-1">
        @foreach ($items as $item)
        @continue(isset($item['can']) && ! auth()->user()->can($item['can']))
            @php
                $active = isset($item['pattern']) && request()->routeIs($item['pattern']);
                $href = $item['route'] ? route($item['route']) : '#';
                $classes = $active
                    ? 'bg-sky-50 text-sky-700'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900';
            @endphp

            <a href="{{ $href }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition {{ $classes }}">
                @if (!empty($item['logo']))
                    <x-application-logo class="w-5 h-5" />
                @else
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                    </svg>
                @endif
                <span class="flex-1">{{ $item['label'] }}</span>
                @unless ($item['route'])
                    <span class="text-[10px] font-bold bg-slate-100 text-slate-500 rounded-full px-2 py-0.5">قريباً</span>
                @endunless
            </a>
        @endforeach
    </nav>

    {{-- تسجيل الخروج --}}
    <div class="p-4 border-t border-slate-100">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-rose-600 hover:bg-rose-50 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                </svg>
                تسجيل الخروج
            </button>
        </form>
    </div>
</aside>