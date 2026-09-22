<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start gap-4">
            @if ($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-16 w-16 rounded-lg border border-gray-200 object-cover">
            @else
                <div class="flex h-16 w-16 items-center justify-center rounded-lg border border-dashed border-gray-300 text-gray-300">
                    <i class="fas fa-image text-xl"></i>
                </div>
            @endif
            <div>
                <a href="{{ route('products.index') }}" class="text-xs text-gray-400 hover:text-ink">← Produtos</a>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight text-ink">{{ $product->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $product->sku }} · {{ $product->category?->name ?? 'Sem categoria' }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Editar</a>
            <a href="{{ route('movements.create', ['product' => $product->id, 'type' => 'saida']) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Registrar saída</a>
            <a href="{{ route('movements.create', ['product' => $product->id, 'type' => 'entrada']) }}" class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600">Registrar entrada</a>
        </div>
    </x-slot>

    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <x-card>
            <p class="text-sm text-gray-500">Em estoque</p>
            <p class="mt-2 text-3xl font-semibold tracking-tight text-ink">{{ $product->quantity }}</p>
            <div class="mt-2 flex items-center gap-2 text-xs text-gray-400"><x-stock-badge :status="$product->stock_status" /> mín. {{ $product->min_quantity }}</div>
        </x-card>
        <x-stat label="Preço de custo" :value="brl($product->cost_price)" />
        <x-stat label="Preço de venda" :value="brl($product->sale_price)" />
        <x-stat label="Valor em estoque" :value="brl($product->quantity * (float) $product->cost_price)" hint="A preço de custo" />
    </div>

    <x-card class="mt-8">
        <h2 class="mb-4 font-semibold text-ink">Histórico de movimentações</h2>
        @include('movements._table', ['movements' => $movements, 'compact' => 'product'])
        <div class="mt-6">{{ $movements->links() }}</div>
    </x-card>
</x-app-layout>
