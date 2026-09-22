<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function query()
    {
        return Product::query()->with('category')->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'Produto', 'SKU', 'Categoria', 'Preço de custo', 'Preço de venda',
            'Quantidade', 'Estoque mínimo', 'Situação', 'Valor em estoque (custo)',
        ];
    }

    /** @param  Product  $product */
    public function map($product): array
    {
        return [
            $product->name,
            $product->sku,
            $product->category?->name,
            (float) $product->cost_price,
            (float) $product->sale_price,
            $product->quantity,
            $product->min_quantity,
            ['ok' => 'OK', 'baixo' => 'Baixo', 'critico' => 'Sem estoque'][$product->stock_status],
            round($product->quantity * (float) $product->cost_price, 2),
        ];
    }
}
