<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-ink">Produtos</h1>
            <p class="mt-1 text-sm text-gray-500">Cadastro e situação do estoque.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('categories.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Categorias</a>
            <a href="{{ route('reports.stock.excel') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Exportar Excel</a>
            <a href="{{ route('products.create') }}" class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600">Novo produto</a>
        </div>
    </x-slot>

    <x-card>
        <form method="GET" class="mb-6 flex flex-wrap items-center gap-3">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por nome ou SKU"
                   class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 sm:w-72">
            <select name="category" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500" onchange="this.form.submit()">
                <option value="">Todas as categorias</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500" onchange="this.form.submit()">
                <option value="">Qualquer situação</option>
                <option value="ok" @selected(request('status') === 'ok')>Estoque OK</option>
                <option value="baixo" @selected(request('status') === 'baixo')>Estoque baixo</option>
                <option value="critico" @selected(request('status') === 'critico')>Sem estoque</option>
            </select>
            <button class="rounded-lg bg-ink px-4 py-2 text-sm font-medium text-white hover:bg-ink-light">Buscar</button>
            @if (request()->hasAny(['q', 'category', 'status']))
                <a href="{{ route('products.index') }}" class="text-sm text-gray-500 hover:text-ink">Limpar</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="text-xs uppercase tracking-wide text-gray-400">
                    <tr>
                        <th class="py-3 pr-4 font-medium">Produto</th>
                        <th class="py-3 pr-4 font-medium">Categoria</th>
                        <th class="py-3 pr-4 text-right font-medium">Custo</th>
                        <th class="py-3 pr-4 text-right font-medium">Venda</th>
                        <th class="py-3 pr-4 text-right font-medium">Estoque</th>
                        <th class="py-3 pr-4 font-medium">Situação</th>
                        <th class="py-3 text-right font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($products as $product)
                        <tr>
                            <td class="py-3 pr-4">
                                <div class="flex items-center gap-3">
                                    @if ($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-9 w-9 rounded-md border border-gray-200 object-cover">
                                    @else
                                        <div class="flex h-9 w-9 items-center justify-center rounded-md border border-dashed border-gray-300 text-gray-300">
                                            <i class="fas fa-image text-xs"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('products.show', $product) }}" class="font-medium text-ink hover:text-brand-600">{{ $product->name }}</a>
                                        <span class="block text-xs text-gray-400">{{ $product->sku }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 pr-4 text-gray-600">{{ $product->category?->name ?? '—' }}</td>
                            <td class="whitespace-nowrap py-3 pr-4 text-right text-gray-600">{{ brl($product->cost_price) }}</td>
                            <td class="whitespace-nowrap py-3 pr-4 text-right text-gray-600">{{ brl($product->sale_price) }}</td>
                            <td class="py-3 pr-4 text-right font-semibold text-ink">{{ $product->quantity }}<span class="font-normal text-gray-400"> / mín. {{ $product->min_quantity }}</span></td>
                            <td class="py-3 pr-4"><x-stock-badge :status="$product->stock_status" /></td>
                            <td class="whitespace-nowrap py-3 text-right">
                                <a href="{{ route('products.edit', $product) }}" class="text-sm text-orange-500 hover:text-orange-600"><i class="fas fa-pen"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-10 text-center text-gray-400">Nenhum produto encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $products->links() }}</div>
    </x-card>
</x-app-layout>
