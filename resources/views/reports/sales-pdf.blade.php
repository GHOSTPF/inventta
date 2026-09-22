<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório de faturamento</title>
    <style>
        @page { margin: 36px 40px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #0F172A; }
        h1 { font-size: 20px; margin: 0; }
        h2 { font-size: 13px; margin: 26px 0 8px; }
        .muted { color: #6b7280; }
        .brand { color: #FF6900; font-weight: bold; font-size: 12px; letter-spacing: 1px; }
        .summary { width: 100%; border-collapse: separate; border-spacing: 8px 0; margin: 20px -8px 0; }
        .summary td { width: 25%; background: #F5F6F7; padding: 12px; border-radius: 6px; }
        .summary .label { color: #6b7280; font-size: 10px; }
        .summary .value { font-size: 16px; font-weight: bold; margin-top: 4px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { text-align: left; font-size: 9px; text-transform: uppercase; color: #6b7280; border-bottom: 1px solid #d1d5db; padding: 6px 4px; }
        table.data td { padding: 6px 4px; border-bottom: 1px solid #eee; }
        .r { text-align: right; }
    </style>
</head>
<body>
    <div class="brand">INVENTTA</div>
    <h1>Relatório de faturamento</h1>
    <div class="muted">Período: {{ $report->label() }} · Gerado em {{ now()->format('d/m/Y H:i') }}</div>

    <table class="summary">
        <tr>
            <td><div class="label">Total faturado</div><div class="value">{{ brl($summary['total']) }}</div></td>
            <td><div class="label">Ticket médio</div><div class="value">{{ brl($summary['average_ticket']) }}</div></td>
            <td><div class="label">Vendas</div><div class="value">{{ $summary['count'] }}</div></td>
            <td><div class="label">Unidades vendidas</div><div class="value">{{ $summary['units'] }}</div></td>
        </tr>
    </table>

    <h2>Produtos mais vendidos</h2>
    <table class="data">
        <thead><tr><th>#</th><th>Produto</th><th>SKU</th><th class="r">Unidades</th><th class="r">Faturamento</th></tr></thead>
        <tbody>
            @forelse ($topProducts as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->name }}</td>
                    <td class="muted">{{ $item->sku }}</td>
                    <td class="r">{{ $item->units }}</td>
                    <td class="r">{{ brl($item->total) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">Sem vendas no período.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Vendas do período</h2>
    <table class="data">
        <thead><tr><th>Data</th><th>Produto</th><th class="r">Qtd.</th><th class="r">Valor unit.</th><th class="r">Total</th></tr></thead>
        <tbody>
            @forelse ($lines as $sale)
                <tr>
                    <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $sale->product->name }}</td>
                    <td class="r">{{ $sale->quantity }}</td>
                    <td class="r">{{ brl($sale->unit_price) }}</td>
                    <td class="r">{{ brl($sale->line_total) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">Sem vendas no período.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
