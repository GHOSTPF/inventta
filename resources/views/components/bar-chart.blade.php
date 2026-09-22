{{-- Gráfico de barras simples em HTML/CSS, sem JS. $series: [rótulo => valor] --}}
@props(['series'])

@php
    $max = max($series ?: [0]);
    $step = max(1, (int) ceil(count($series) / 8));
    $i = 0;
@endphp

@if ($max <= 0)
    <div class="flex h-48 items-center justify-center text-sm text-gray-400">Sem vendas no período.</div>
@else
    <div class="flex h-48 items-end gap-1 border-b border-gray-200">
        @foreach ($series as $label => $value)
            <div class="flex h-full flex-1 items-end" title="{{ $label }}: {{ brl($value) }}">
                <div class="w-full rounded-t bg-brand-400 transition hover:bg-brand-500"
                     style="height: {{ $value > 0 ? max(2, round($value / $max * 100)) : 0 }}%"></div>
            </div>
        @endforeach
    </div>
    <div class="mt-2 flex gap-1 text-[11px] text-gray-400">
        @foreach ($series as $label => $value)
            <div class="flex-1 text-center">{{ $loop->index % $step === 0 ? $label : '' }}</div>
        @endforeach
    </div>
@endif
