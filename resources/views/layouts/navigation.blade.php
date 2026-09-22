@php
    $items = [
        ['Dashboard', 'dashboard', 'dashboard', 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10'],
        ['Produtos', 'products.index', 'products.*', 'M21 8l-9-5-9 5v8l9 5 9-5V8zM3 8l9 5 9-5M12 13v8'],
        ['Movimentações', 'movements.index', 'movements.*', 'M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4'],
        ['Relatórios', 'reports.sales', 'reports.*', 'M9 17v-6m4 6V7m4 10v-3M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z'],
    ];
@endphp

{{-- Fundo escuro do menu mobile --}}
<div x-show="open" x-cloak @click="open = false" class="fixed inset-0 z-40 bg-black/50 lg:hidden"></div>

<aside
    :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-ink text-gray-300 transition-transform duration-200"
>
    <div class="flex items-center gap-3 px-6 py-7">
        <x-application-logo class="h-8 w-8" />
        <span class="text-xl font-bold tracking-tight text-white">Inventta</span>
    </div>

    <nav class="flex-1 space-y-1 px-3">
        @foreach ($items as [$label, $route, $pattern, $icon])
            <a href="{{ route($route) }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                      {{ request()->routeIs($pattern) ? 'bg-white/10 text-brand-400' : 'hover:bg-white/5 hover:text-white' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $icon }}"/></svg>
                {{ $label }}
            </a>
        @endforeach
    </nav>

    <div class="border-t border-white/10 px-3 py-4">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-white/5">
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-500 text-sm font-semibold text-white">
                {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
            </span>
            <span class="min-w-0">
                <span class="block truncate text-sm font-medium text-white">{{ Auth::user()->name }}</span>
                <span class="block text-xs text-gray-400">Meu perfil</span>
            </span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="mt-1 w-full rounded-lg px-3 py-2 text-left text-sm text-gray-400 hover:bg-white/5 hover:text-white">Sair</button>
        </form>
    </div>
</aside>
