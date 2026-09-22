<?php

namespace App\Services;

use App\Models\StockMovement;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Faturamento = saídas de estoque com motivo "venda" (quantidade x preço unitário).
 */
class SalesReport
{
    public const PERIODS = [
        'day' => 'Hoje',
        'week' => 'Semana',
        'month' => 'Mês',
        'custom' => 'Personalizado',
    ];

    public function __construct(
        public readonly string $period,
        public readonly CarbonImmutable $from,
        public readonly CarbonImmutable $to,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $period = $request->query('period', 'month');
        $now = CarbonImmutable::now();

        if ($period === 'custom') {
            try {
                $from = CarbonImmutable::parse($request->query('from'))->startOfDay();
                $to = CarbonImmutable::parse($request->query('to'))->endOfDay();
            } catch (\Throwable) {
                $from = $now->startOfMonth();
                $to = $now->endOfDay();
            }

            if ($from->gt($to)) {
                [$from, $to] = [$to->startOfDay(), $from->endOfDay()];
            }

            return new self('custom', $from, $to);
        }

        return match ($period) {
            'day' => new self('day', $now->startOfDay(), $now->endOfDay()),
            'week' => new self('week', $now->startOfWeek(), $now->endOfWeek()),
            default => new self('month', $now->startOfMonth(), $now->endOfMonth()),
        };
    }

    public function label(): string
    {
        return $this->from->isSameDay($this->to)
            ? $this->from->format('d/m/Y')
            : $this->from->format('d/m/Y').' a '.$this->to->format('d/m/Y');
    }

    /** Parâmetros de URL que reproduzem este período (para links de exportação). */
    public function queryParams(): array
    {
        return $this->period === 'custom'
            ? ['period' => 'custom', 'from' => $this->from->toDateString(), 'to' => $this->to->toDateString()]
            : ['period' => $this->period];
    }

    private function sales(): Builder
    {
        return StockMovement::query()
            ->where('stock_movements.type', StockMovement::TYPE_OUT)
            ->where('stock_movements.reason', StockMovement::REASON_SALE)
            ->whereBetween('stock_movements.created_at', [$this->from, $this->to]);
    }

    /** @return array{total: float, units: int, count: int, average_ticket: float} */
    public function summary(): array
    {
        $row = $this->sales()
            ->selectRaw('COALESCE(SUM(quantity * unit_price), 0) as total, COALESCE(SUM(quantity), 0) as units, COUNT(*) as count')
            ->first();

        $total = (float) $row->total;
        $count = (int) $row->count;

        return [
            'total' => $total,
            'units' => (int) $row->units,
            'count' => $count,
            'average_ticket' => $count > 0 ? $total / $count : 0.0,
        ];
    }

    /** Cada venda individual do período, da mais recente para a mais antiga. */
    public function lines()
    {
        return $this->sales()->with('product')->latest()->latest('id')->get();
    }

    public function topProducts(int $limit = 10)
    {
        return $this->sales()
            ->join('products', 'products.id', '=', 'stock_movements.product_id')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total')
            ->limit($limit)
            ->get([
                'products.name',
                'products.sku',
                DB::raw('SUM(stock_movements.quantity) as units'),
                DB::raw('SUM(stock_movements.quantity * stock_movements.unit_price) as total'),
            ]);
    }

    /**
     * Série para o gráfico: por hora quando o período é um único dia, senão por dia.
     *
     * @return array<string, float> rótulo => faturamento
     */
    public function series(): array
    {
        if ($this->from->isSameDay($this->to)) {
            $series = array_fill_keys(array_map(fn ($h) => sprintf('%02dh', $h), range(0, 23)), 0.0);

            foreach ($this->sales()->get(['quantity', 'unit_price', 'created_at']) as $sale) {
                $series[$sale->created_at->format('H').'h'] += $sale->line_total;
            }

            return $series;
        }

        $totals = $this->sales()
            ->selectRaw('DATE(created_at) as day, SUM(quantity * unit_price) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'day');

        $series = [];
        for ($day = $this->from->startOfDay(); $day->lte($this->to); $day = $day->addDay()) {
            $series[$day->format('d/m')] = (float) ($totals[$day->toDateString()] ?? 0);
        }

        return $series;
    }
}
