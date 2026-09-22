@props(['status'])

@php
    [$label, $classes] = match ($status) {
        'critico' => ['Sem estoque', 'bg-red-50 text-red-700 ring-red-600/20'],
        'baixo' => ['Estoque baixo', 'bg-amber-50 text-amber-700 ring-amber-600/20'],
        default => ['OK', 'bg-green-50 text-green-700 ring-green-600/20'],
    };
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $classes }}">{{ $label }}</span>
