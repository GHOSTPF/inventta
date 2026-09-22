@php $compact = $compact ?? false; @endphp

<div class="overflow-x-auto">
    <table class="min-w-full text-left text-sm">
        <thead class="text-xs uppercase tracking-wide text-gray-400">
            <tr>
                <th class="whitespace-nowrap py-3 pr-4 font-medium">Data</th>
                @unless ($compact === 'product')
                    <th class="py-3 pr-4 font-medium">Produto</th>
                @endunless
                <th class="py-3 pr-4 font-medium">Tipo</th>
                <th class="py-3 pr-4 font-medium">Motivo</th>
                <th class="py-3 pr-4 text-right font-medium">Qtd.</th>
                @unless ($compact === true)
                    <th class="py-3 pr-4 text-right font-medium">Valor unit.</th>
                    <th class="py-3 font-medium">Usuário</th>
                @endunless
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($movements as $movement)
                <tr>
                    <td class="whitespace-nowrap py-3 pr-4 text-gray-500">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                    @unless ($compact === 'product')
                        <td class="py-3 pr-4">
                            <a href="{{ route('products.show', $movement->product_id) }}" class="font-medium text-ink hover:text-brand-600">{{ $movement->product->name }}</a>
                        </td>
                    @endunless
                    <td class="py-3 pr-4">
                        @if ($movement->type === 'entrada')
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Entrada</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-500/20">Saída</span>
                        @endif
                    </td>
                    <td class="py-3 pr-4 text-gray-600">{{ $movement->reason_label }}</td>
                    <td class="py-3 pr-4 text-right font-semibold {{ $movement->type === 'entrada' ? 'text-green-600' : 'text-ink' }}">
                        {{ $movement->type === 'entrada' ? '+' : '−' }}{{ $movement->quantity }}
                    </td>
                    @unless ($compact === true)
                        <td class="whitespace-nowrap py-3 pr-4 text-right text-gray-600">{{ brl($movement->unit_price) }}</td>
                        <td class="py-3 text-gray-500">{{ $movement->user?->name ?? '—' }}</td>
                    @endunless
                </tr>
            @empty
                <tr><td colspan="7" class="py-10 text-center text-gray-400">Nenhuma movimentação registrada.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
