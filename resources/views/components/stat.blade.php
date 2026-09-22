@props(['label', 'value', 'hint' => null, 'alert' => false])

<x-card>
    <p class="text-sm text-gray-500">{{ $label }}</p>
    <p class="mt-2 text-3xl font-semibold tracking-tight {{ $alert ? 'text-red-600' : 'text-ink' }}">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs text-gray-400">{{ $hint }}</p>
    @endif
</x-card>
