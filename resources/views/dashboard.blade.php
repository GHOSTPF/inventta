<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-ink">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Visão geral do seu estoque e das vendas.</p>
        </div>
        <a href="{{ route('movements.create', ['type' => 'saida']) }}"
           class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600">
            Registrar movimentação
        </a>
    </x-slot>

    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat label="Itens em estoque" :value="number_format($totalItems, 0, ',', '.')" hint="Unidades somadas de todos os produtos" />
        <x-stat label="Valor do estoque" :value="brl($stockValue)" hint="A preço de custo" />
        <x-stat label="Faturamento do mês" :value="brl($monthRevenue)" :hint="now()->translatedFormat('F \d\e Y')" />
        <x-stat label="Estoque baixo ou crítico" :value="$lowStockCount" :alert="$lowStockCount > 0" hint="Produtos no mínimo ou abaixo" />
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <x-card class="lg:col-span-2">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="font-semibold text-ink">Faturamento</h2>
                    <p class="text-xs text-gray-400">{{ $chartReport->label() }}</p>
                </div>
                <div class="inline-flex rounded-lg bg-gray-100 p-1 text-sm">
                    @foreach (['day' => 'Dia', 'week' => 'Semana', 'month' => 'Mês'] as $key => $label)
                        <a href="{{ route('dashboard', ['period' => $key]) }}"
                           class="rounded-md px-3 py-1 {{ $chartReport->period === $key ? 'bg-white font-medium text-ink shadow-sm' : 'text-gray-500 hover:text-ink' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
            <x-bar-chart :series="$series" />
        </x-card>

        <x-card>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold text-ink">Repor em breve</h2>
                <a href="{{ route('products.index', ['status' => 'baixo']) }}" class="text-xs text-brand-600 hover:underline">Ver todos</a>
            </div>
            <ul class="divide-y divide-gray-100">
                @forelse ($lowStock as $product)
                    <li class="flex items-center justify-between gap-3 py-3">
                        <div class="min-w-0">
                            <a href="{{ route('products.show', $product) }}" class="block truncate text-sm font-medium text-ink hover:text-brand-600">{{ $product->name }}</a>
                            <span class="text-xs text-gray-400">mín. {{ $product->min_quantity }}</span>
                        </div>
                        <div class="text-right">
                            <span class="block text-sm font-semibold {{ $product->quantity <= 0 ? 'text-red-600' : 'text-amber-600' }}">{{ $product->quantity }} un.</span>
                        </div>
                    </li>
                @empty
                    <li class="py-6 text-center text-sm text-gray-400">Tudo em ordem por aqui.</li>
                @endforelse
            </ul>
        </x-card>
    </div>

    <x-card class="mt-8">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="font-semibold text-ink">Últimas movimentações</h2>
            <a href="{{ route('movements.index') }}" class="text-xs text-brand-600 hover:underline">Ver histórico</a>
        </div>
        @include('movements._table', ['movements' => $recentMovements, 'compact' => true])
    </x-card>
</x-app-layout>
