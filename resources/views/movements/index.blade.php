<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-ink">Movimentações</h1>
            <p class="mt-1 text-sm text-gray-500">Histórico de entradas e saídas de estoque.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('movements.create', ['type' => 'saida']) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Registrar saída</a>
            <a href="{{ route('movements.create', ['type' => 'entrada']) }}" class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600">Registrar entrada</a>
        </div>
    </x-slot>

    <x-card>
        <form method="GET" class="mb-6 flex flex-wrap items-center gap-3">
            <select name="product" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500" onchange="this.form.submit()">
                <option value="">Todos os produtos</option>
                @foreach ($products as $p)
                    <option value="{{ $p->id }}" @selected(request('product') == $p->id)>{{ $p->name }} ({{ $p->sku }})</option>
                @endforeach
            </select>
            <select name="type" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500" onchange="this.form.submit()">
                <option value="">Entradas e saídas</option>
                <option value="entrada" @selected(request('type') === 'entrada')>Somente entradas</option>
                <option value="saida" @selected(request('type') === 'saida')>Somente saídas</option>
            </select>
            @if (request()->hasAny(['product', 'type']))
                <a href="{{ route('movements.index') }}" class="text-sm text-gray-500 hover:text-ink">Limpar</a>
            @endif
        </form>

        @include('movements._table', ['movements' => $movements])

        <div class="mt-6">{{ $movements->links() }}</div>
    </x-card>
</x-app-layout>
