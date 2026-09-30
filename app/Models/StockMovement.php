<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    public const UPDATED_AT = null;

    public const TYPE_IN = 'entrada';
    public const TYPE_OUT = 'saida';

    public const REASON_SALE = 'venda';

    public const REASONS = [
        self::TYPE_IN => ['compra' => 'Compra', 'reposicao' => 'Reposição', 'ajuste' => 'Ajuste de inventário'],
        self::TYPE_OUT => ['venda' => 'Venda', 'perda' => 'Perda / avaria', 'ajuste' => 'Ajuste de inventário'],
    ];

    protected $fillable = ['product_id', 'type', 'quantity', 'reason', 'unit_price', 'unit_cost', 'notes', 'user_id'];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getReasonLabelAttribute(): string
    {
        return self::REASONS[$this->type][$this->reason] ?? $this->reason;
    }

    public function getLineTotalAttribute(): float
    {
        return round($this->quantity * (float) $this->unit_price, 2);
    }

    public function getLineCostAttribute(): float
    {
        return round($this->quantity * (float) $this->unit_cost, 2);
    }

    public function getLineProfitAttribute(): float
    {
        return round($this->line_total - $this->line_cost, 2);
    }
}
