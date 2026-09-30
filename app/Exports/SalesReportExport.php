<?php

namespace App\Exports;

use App\Services\SalesReport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesReportExport implements FromArray, ShouldAutoSize, WithHeadings
{
    public function __construct(private SalesReport $report) {}

    public function headings(): array
    {
        return ['Data', 'Produto', 'SKU', 'Quantidade', 'Preço unitário', 'Total', 'Custo unitário', 'Custo total', 'Lucro'];
    }

    public function array(): array
    {
        $rows = $this->report->lines()->map(fn ($sale) => [
            $sale->created_at->format('d/m/Y H:i'),
            $sale->product->name,
            $sale->product->sku,
            $sale->quantity,
            (float) $sale->unit_price,
            $sale->line_total,
            (float) $sale->unit_cost,
            $sale->line_cost,
            $sale->line_profit,
        ])->all();

        $summary = $this->report->summary();
        $rows[] = [];
        $rows[] = ['Total faturado', '', '', $summary['units'], '', $summary['total'], '', $summary['cost'], $summary['profit']];
        $rows[] = ['Ticket médio', '', '', '', '', round($summary['average_ticket'], 2)];
        $rows[] = ['Margem de lucro (%)', '', '', '', '', round($summary['margin'], 1)];

        return $rows;
    }
}
