@php $isEdit = $product->exists; @endphp

<form method="POST" action="{{ $isEdit ? route('products.update', $product) : route('products.store') }}" class="space-y-6" enctype="multipart/form-data">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="grid gap-6 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <x-input-label for="image" value="Imagem do produto" />
            <div class="mt-1 flex items-center gap-4">
                @if ($isEdit && $product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-20 w-20 rounded-lg border border-gray-200 object-cover">
                @else
                    <div class="flex h-20 w-20 items-center justify-center rounded-lg border border-dashed border-gray-300 text-gray-300">
                        <i class="fas fa-image text-2xl"></i>
                    </div>
                @endif
                <div class="flex-1">
                    <input id="image" name="image" type="file" accept="image/*" class="block w-full text-sm text-gray-600">
                    <p class="mt-1 text-xs text-gray-400">JPG, PNG ou WEBP até 4MB.</p>
                    @if ($isEdit && $product->image_url)
                        <label class="mt-2 inline-flex items-center gap-2 text-xs text-gray-500">
                            <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                            Remover imagem atual
                        </label>
                    @endif
                </div>
            </div>
            <x-input-error :messages="$errors->get('image')" class="mt-2" />
        </div>

        <div class="sm:col-span-2">
            <x-input-label for="name" value="Nome" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $product->name)" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="sku" value="SKU / código" />
            <x-text-input id="sku" name="sku" type="text" class="mt-1 block w-full" :value="old('sku', $product->sku)" required />
            <x-input-error :messages="$errors->get('sku')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="category_id" value="Categoria" />
            <select id="category_id" name="category_id" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Sem categoria</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="cost_price" value="Preço de custo (R$)" />
            <x-text-input id="cost_price" name="cost_price" type="text" inputmode="decimal" class="mt-1 block w-full" :value="old('cost_price', $product->cost_price)" required />
            <x-input-error :messages="$errors->get('cost_price')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="sale_price" value="Preço de venda (R$)" />
            <x-text-input id="sale_price" name="sale_price" type="text" inputmode="decimal" class="mt-1 block w-full" :value="old('sale_price', $product->sale_price)" required />
            <x-input-error :messages="$errors->get('sale_price')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="quantity" :value="$isEdit ? 'Quantidade em estoque' : 'Estoque inicial'" />
            <x-text-input id="quantity" name="quantity" type="number" min="0" step="1" class="mt-1 block w-full" :value="old('quantity', $product->quantity)" required />
            @if ($isEdit)
                <p class="mt-1 text-xs text-gray-400">Alterar aqui gera um ajuste no histórico. Para compras e vendas, use Movimentações.</p>
            @endif
            <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="min_quantity" value="Estoque mínimo" />
            <x-text-input id="min_quantity" name="min_quantity" type="number" min="0" step="1" class="mt-1 block w-full" :value="old('min_quantity', $product->min_quantity)" required />
            <x-input-error :messages="$errors->get('min_quantity')" class="mt-2" />
        </div>
    </div>

    <div class="flex items-center gap-3">
        <x-primary-button>{{ $isEdit ? 'Salvar alterações' : 'Cadastrar produto' }}</x-primary-button>
        <a href="{{ $isEdit ? route('products.show', $product) : route('products.index') }}" class="text-sm text-gray-500 hover:text-ink">Cancelar</a>
    </div>
</form>
