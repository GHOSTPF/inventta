@php
    $reasons = \App\Models\StockMovement::REASONS;
    $productData = $products->mapWithKeys(fn ($p) => [$p->id => [
        'stock' => $p->quantity, 'cost' => (float) $p->cost_price, 'sale' => (float) $p->sale_price,
    ]]);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-ink">Nova movimentação</h1>
            <p class="mt-1 text-sm text-gray-500">A quantidade do produto é atualizada automaticamente.</p>
        </div>
    </x-slot>

    <x-card class="max-w-2xl"
        x-data="{
            type: '{{ old('type', $type) }}',
            reason: '{{ old('reason', $type === 'saida' ? 'venda' : 'compra') }}',
            product: '{{ old('product_id', $selected) }}',
            price: '{{ old('unit_price') }}',
            reasons: @js($reasons),
            products: @js($productData),
            get info() { return this.products[this.product] ?? null },
            setType(t) { this.type = t; this.reason = Object.keys(this.reasons[t])[0]; this.price = ''; },
            get suggested() {
                if (!this.info) return '';
                return (this.reason === 'venda' ? this.info.sale : this.info.cost).toFixed(2).replace('.', ',');
            },
        }">
        <form method="POST" action="{{ route('movements.store') }}" class="space-y-6">
            @csrf

            <div>
                <x-input-label value="Tipo" />
                <div class="mt-1 grid grid-cols-2 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="entrada" x-model="type" @change="setType('entrada')" class="peer sr-only">
                        <span class="block rounded-lg border border-gray-300 px-4 py-2.5 text-center text-sm font-medium text-gray-600 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700">Entrada</span>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="saida" x-model="type" @change="setType('saida')" class="peer sr-only">
                        <span class="block rounded-lg border border-gray-300 px-4 py-2.5 text-center text-sm font-medium text-gray-600 peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-700">Saída</span>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('type')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="product_id" value="Produto" />
                <select id="product_id" name="product_id" x-model="product" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                    <option value="">Selecione…</option>
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                    @endforeach
                </select>
                <p x-show="info" x-cloak class="mt-1 text-xs text-gray-400">Em estoque: <span x-text="info?.stock"></span> un.</p>
                <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <x-input-label for="reason" value="Motivo" />
                    <select id="reason" name="reason" x-model="reason" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <template x-for="(label, key) in reasons[type]" :key="key">
                            <option :value="key" x-text="label" :selected="key === reason"></option>
                        </template>
                    </select>
                    <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="quantity" value="Quantidade" />
                    <x-text-input id="quantity" name="quantity" type="number" min="1" step="1" class="mt-1 block w-full" :value="old('quantity')" required />
                    <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="unit_price" value="Valor unitário (opcional)" />
                <x-text-input id="unit_price" name="unit_price" type="text" inputmode="decimal" class="mt-1 block w-full"
                              x-model="price" x-bind:placeholder="suggested ? 'Padrão: R$ ' + suggested : 'Usa o preço cadastrado'" />
                <p class="mt-1 text-xs text-gray-400">Vendas usam o preço de venda; as demais, o preço de custo. Preencha só para alterar.</p>
                <x-input-error :messages="$errors->get('unit_price')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="notes" value="Observações (opcional)" />
                <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('notes') }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>

            <div class="flex items-center gap-3">
                <x-primary-button>Registrar</x-primary-button>
                <a href="{{ route('movements.index') }}" class="text-sm text-gray-500 hover:text-ink">Cancelar</a>
            </div>
        </form>
    </x-card>
</x-app-layout>
