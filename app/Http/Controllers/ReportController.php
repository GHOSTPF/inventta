<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Exports\SalesReportExport;
use App\Services\SalesReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $report = SalesReport::fromRequest($request);

        return view('reports.sales', [
            'report' => $report,
            'summary' => $report->summary(),
            'topProducts' => $report->topProducts(),
            'series' => $report->series(),
        ]);
    }

    public function salesPdf(Request $request)
    {
        $report = SalesReport::fromRequest($request);

        return Pdf::loadView('reports.sales-pdf', [
            'report' => $report,
            'summary' => $report->summary(),
            'topProducts' => $report->topProducts(),
            'lines' => $report->lines(),
        ])->download('faturamento-'.$report->from->format('Ymd').'-'.$report->to->format('Ymd').'.pdf');
    }

    public function salesExcel(Request $request)
    {
        $report = SalesReport::fromRequest($request);

        return Excel::download(
            new SalesReportExport($report),
            'faturamento-'.$report->from->format('Ymd').'-'.$report->to->format('Ymd').'.xlsx',
        );
    }

    public function stockExcel()
    {
        return Excel::download(new ProductsExport, 'estoque-'.now()->format('Ymd').'.xlsx');
    }
}
