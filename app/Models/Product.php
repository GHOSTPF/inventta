<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'sku', 'category_id', 'cost_price', 'sale_price', 'quantity', 'min_quantity', 'image_path',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'quantity' => 'integer',
            'min_quantity' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Registra uma movimentação e atualiza a quantidade em estoque de forma atômica.
     * Sem $unitPrice, usa o preço de venda (venda) ou o de custo (demais motivos).
     */
    public function registerMovement(
        string $type,
        int $quantity,
        string $reason,
        ?float $unitPrice = null,
        ?string $notes = null,
        ?int $userId = null,
        ?\DateTimeInterface $at = null,
    ): StockMovement {
        return DB::transaction(function () use ($type, $quantity, $reason, $unitPrice, $notes, $userId, $at) {
            $product = static::whereKey($this->getKey())->lockForUpdate()->firstOrFail();

            if ($type === StockMovement::TYPE_OUT && $quantity > $product->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => "Estoque insuficiente: há apenas {$product->quantity} unidade(s) de {$product->name}.",
                ]);
            }

            $product->quantity += $type === StockMovement::TYPE_IN ? $quantity : -$quantity;
            $product->save();

            $movement = $product->movements()->create([
                'type' => $type,
                'quantity' => $quantity,
                'reason' => $reason,
                'unit_price' => $unitPrice ?? ($reason === StockMovement::REASON_SALE ? $product->sale_price : $product->cost_price),
                'notes' => $notes,
                'user_id' => $userId,
            ]);

            if ($at) {
                $movement->created_at = $at;
                $movement->save();
            }

            $this->quantity = $product->quantity;

            return $movement;
        });
    }

    /** Sem estoque. */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('quantity', '<=', 0);
    }

    /** Baixo: acima de zero, mas dentro do estoque mínimo. */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->where('quantity', '>', 0)->whereColumn('quantity', '<=', 'min_quantity');
    }

    /** Baixo ou crítico (inclui sem estoque). */
    public function scopeNeedsRestock(Builder $query): Builder
    {
        return $query->whereColumn('quantity', '<=', 'min_quantity');
    }

    /** ok | baixo | critico */
    public function getStockStatusAttribute(): string
    {
        if ($this->quantity <= 0) {
            return 'critico';
        }

        return $this->quantity <= $this->min_quantity ? 'baixo' : 'ok';
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }
}
