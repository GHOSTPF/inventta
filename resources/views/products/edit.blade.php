<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold tracking-tight text-ink">Editar produto</h1>
    </x-slot>

    <x-card class="max-w-3xl">
        @include('products._form')
    </x-card>

    <x-card class="mt-6 max-w-3xl" x-data="{ confirming: false }">
        <h2 class="font-semibold text-ink">Remover produto</h2>
        <p class="mt-1 text-sm text-gray-500">O produto deixa de aparecer nas listas, mas o histórico de vendas é mantido.</p>
        <form method="POST" action="{{ route('products.destroy', $product) }}" class="mt-4">
            @csrf @method('DELETE')
            <button type="button" x-show="!confirming" @click="confirming = true" class="rounded-lg border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">Remover…</button>
            <div x-show="confirming" x-cloak class="flex items-center gap-3">
                <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Confirmar remoção</button>
                <button type="button" @click="confirming = false" class="text-sm text-gray-500 hover:text-ink">Cancelar</button>
            </div>
        </form>
    </x-card>
</x-app-layout>
