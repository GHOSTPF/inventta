<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold tracking-tight text-ink">Novo produto</h1>
    </x-slot>

    <x-card class="max-w-3xl">
        @include('products._form')
    </x-card>
</x-app-layout>
