<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('products.index') }}" class="text-xs text-gray-400 hover:text-ink">← Produtos</a>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight text-ink">Categorias</h1>
        </div>
    </x-slot>

    <div class="grid max-w-4xl gap-6 md:grid-cols-5">
        <x-card class="md:col-span-3">
            <ul class="divide-y divide-gray-100">
                @forelse ($categories as $category)
                    <li class="flex items-center justify-between py-3">
                        <div>
                            <span class="font-medium text-ink">{{ $category->name }}</span>
                            <span class="ml-2 text-xs text-gray-400">{{ $category->products_count }} produto(s)</span>
                        </div>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Remover esta categoria? Os produtos ficarão sem categoria.')">
                            @csrf @method('DELETE')
                            <button class="text-sm text-gray-400 hover:text-red-600">Remover</button>
                        </form>
                    </li>
                @empty
                    <li class="py-6 text-center text-sm text-gray-400">Nenhuma categoria ainda.</li>
                @endforelse
            </ul>
        </x-card>

        <x-card class="md:col-span-2 self-start">
            <form method="POST" action="{{ route('categories.store') }}" class="space-y-4">
                @csrf
                <div>
                    <x-input-label for="name" value="Nova categoria" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <x-primary-button>Adicionar</x-primary-button>
            </form>
        </x-card>
    </div>
</x-app-layout>
