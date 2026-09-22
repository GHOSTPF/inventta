<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-ink">Relatório de faturamento</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $report->label() }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('reports.stock.excel') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Estoque (Excel)</a>
            <a href="{{ route('reports.sales.excel', $report->queryParams()) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Faturamento (Excel)</a>
            <a href="{{ route('reports.sales.pdf', $report->queryParams()) }}" class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600">Faturamento (PDF)</a>
        </div>
    </x-slot>

    <x-card class="mb-8">
        <form method="GET" x-data="{ period: '{{ $report->period }}' }" class="flex flex-wrap items-end gap-3">
            <div class="inline-flex rounded-lg bg-gray-100 p-1 text-sm">
                @foreach (\App\Services\SalesReport::PERIODS as $key => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="period" value="{{ $key }}" x-model="period" class="peer sr-only" @change="if (period !== 'custom') $el.form.submit()">
                        <span class="block rounded-md px-3 py-1 text-gray-500 peer-checked:bg-white peer-checked:font-medium peer-checked:text-ink peer-checked:shadow-sm">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <div x-show="period === 'custom'" x-cloak class="flex flex-wrap items-end gap-3">
                <input type="date" name="from" value="{{ $report->from->toDateString() }}" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <span class="pb-2 text-sm text-gray-400">até</span>
                <input type="date" name="to" value="{{ $report->to->toDateString() }}" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <button class="rounded-lg bg-ink px-4 py-2 text-sm font-medium text-white hover:bg-ink-light">Aplicar</button>
            </div>
        </form>
    </x-card>

    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat label="Total faturado" :value="brl($summary['total'])" />
        <x-stat label="Ticket médio" :value="brl($summary['average_ticket'])" hint="Por venda registrada" />
        <x-stat label="Vendas" :value="number_format($summary['count'], 0, ',', '.')" />
        <x-stat label="Unidades vendidas" :value="number_format($summary['units'], 0, ',', '.')" />
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-5">
        <x-card class="lg:col-span-3">
            <h2 class="mb-6 font-semibold text-ink">Faturamento no período</h2>
            <x-bar-chart :series="$series" />
        </x-card>

        <x-card class="lg:col-span-2">
            <h2 class="mb-4 font-semibold text-ink">Produtos mais vendidos</h2>
            <ol class="divide-y divide-gray-100">
                @forelse ($topProducts as $item)
                    <li class="flex items-center justify-between gap-3 py-3">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-medium text-gray-500">{{ $loop->iteration }}</span>
                            <div class="min-w-0">
                                <span class="block truncate text-sm font-medium text-ink">{{ $item->name }}</span>
                                <span class="text-xs text-gray-400">{{ $item->units }} un.</span>
                            </div>
                        </div>
                        <span class="whitespace-nowrap text-sm font-semibold text-ink">{{ brl($item->total) }}</span>
                    </li>
                @empty
                    <li class="py-6 text-center text-sm text-gray-400">Sem vendas no período.</li>
                @endforelse
            </ol>
        </x-card>
    </div>
</x-app-layout>
