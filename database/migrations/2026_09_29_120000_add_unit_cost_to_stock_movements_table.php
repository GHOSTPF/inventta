<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // Custo unitário do produto no momento da movimentação (base do cálculo de lucro).
            $table->decimal('unit_cost', 12, 2)->nullable()->after('unit_price');
        });

        // Movimentações antigas: usa o custo atual do produto como melhor estimativa.
        DB::table('stock_movements')->whereNull('unit_cost')->update([
            'unit_cost' => DB::raw('(SELECT cost_price FROM products WHERE products.id = stock_movements.product_id)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn('unit_cost');
        });
    }
};
