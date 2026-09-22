<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Services\SalesReport;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $stock = Product::query()
            ->selectRaw('COALESCE(SUM(quantity), 0) as items, COALESCE(SUM(quantity * cost_price), 0) as value')
            ->first();

        $now = CarbonImmutable::now();
        $monthRevenue = (new SalesReport('month', $now->startOfMonth(), $now->endOfMonth()))->summary()['total'];

        // O gráfico segue o filtro (dia/semana/mês); os cards são sempre do mês.
        $chartReport = SalesReport::fromRequest($request->merge([
            'period' => in_array($request->query('period'), ['day', 'week'], true) ? $request->query('period') : 'month',
        ]));

        return view('dashboard', [
            'totalItems' => (int) $stock->items,
            'stockValue' => (float) $stock->value,
            'monthRevenue' => $monthRevenue,
            'lowStockCount' => Product::needsRestock()->count(),
            'lowStock' => Product::needsRestock()->with('category')->orderBy('quantity')->limit(6)->get(),
            'recentMovements' => StockMovement::with('product')->latest()->latest('id')->limit(6)->get(),
            'chartReport' => $chartReport,
            'series' => $chartReport->series(),
        ]);
    }
}
