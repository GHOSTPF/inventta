<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->product = Product::create([
            'name' => 'Camiseta', 'sku' => 'CAM-1', 'category_id' => Category::create(['name' => 'Roupas'])->id,
            'cost_price' => 40, 'sale_price' => 100, 'quantity' => 10, 'min_quantity' => 3,
        ]);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/produtos')->assertRedirect('/login');
    }

    public function test_entry_and_exit_update_stock(): void
    {
        $this->actingAs($this->user)->post('/movimentacoes', [
            'product_id' => $this->product->id, 'type' => 'entrada', 'reason' => 'compra', 'quantity' => 5,
        ])->assertRedirect('/movimentacoes');
        $this->assertSame(15, $this->product->fresh()->quantity);

        $this->actingAs($this->user)->post('/movimentacoes', [
            'product_id' => $this->product->id, 'type' => 'saida', 'reason' => 'venda', 'quantity' => 4,
        ])->assertRedirect('/movimentacoes');
        $this->assertSame(11, $this->product->fresh()->quantity);

        // Venda usa o preço de venda; compra, o de custo.
        $this->assertSame('40.00', StockMovement::where('reason', 'compra')->value('unit_price'));
        $this->assertSame('100.00', StockMovement::where('reason', 'venda')->value('unit_price'));
    }

    public function test_cannot_sell_more_than_in_stock(): void
    {
        $this->actingAs($this->user)->post('/movimentacoes', [
            'product_id' => $this->product->id, 'type' => 'saida', 'reason' => 'venda', 'quantity' => 11,
        ])->assertSessionHasErrors('quantity');

        $this->assertSame(10, $this->product->fresh()->quantity);
        $this->assertSame(0, StockMovement::count());
    }

    public function test_reason_must_match_type(): void
    {
        $this->actingAs($this->user)->post('/movimentacoes', [
            'product_id' => $this->product->id, 'type' => 'entrada', 'reason' => 'venda', 'quantity' => 1,
        ])->assertSessionHasErrors('reason');
    }

    public function test_creating_product_with_initial_stock_logs_a_movement(): void
    {
        $this->actingAs($this->user)->post('/produtos', [
            'name' => 'Boné', 'sku' => 'BON-1', 'cost_price' => '10,50', 'sale_price' => '25,90',
            'quantity' => 7, 'min_quantity' => 2,
        ])->assertRedirect();

        $product = Product::where('sku', 'BON-1')->first();
        $this->assertSame(7, $product->quantity);
        $this->assertSame('10.50', $product->cost_price);
        $this->assertSame(1, $product->movements()->count());
    }

    public function test_editing_quantity_creates_adjustment(): void
    {
        $this->actingAs($this->user)->put("/produtos/{$this->product->id}", [
            'name' => 'Camiseta', 'sku' => 'CAM-1', 'cost_price' => 40, 'sale_price' => 100,
            'quantity' => 6, 'min_quantity' => 3,
        ])->assertRedirect();

        $this->assertSame(6, $this->product->fresh()->quantity);
        $movement = $this->product->movements()->first();
        $this->assertSame(['saida', 'ajuste', 4], [$movement->type, $movement->reason, $movement->quantity]);
    }

    public function test_sku_must_be_unique(): void
    {
        $this->actingAs($this->user)->post('/produtos', [
            'name' => 'Outro', 'sku' => 'CAM-1', 'cost_price' => 1, 'sale_price' => 2,
            'quantity' => 0, 'min_quantity' => 0,
        ])->assertSessionHasErrors('sku');
    }

    public function test_product_filters(): void
    {
        Product::create(['name' => 'Zerado', 'sku' => 'Z-1', 'cost_price' => 1, 'sale_price' => 2, 'quantity' => 0, 'min_quantity' => 2]);
        Product::create(['name' => 'Baixinho', 'sku' => 'B-1', 'cost_price' => 1, 'sale_price' => 2, 'quantity' => 2, 'min_quantity' => 2]);

        $this->actingAs($this->user)->get('/produtos?status=critico')
            ->assertSee('Zerado')->assertDontSee('Baixinho')->assertDontSee('Camiseta');
        $this->actingAs($this->user)->get('/produtos?status=baixo')
            ->assertSee('Baixinho')->assertDontSee('Zerado');
        $this->actingAs($this->user)->get('/produtos?q=cam')->assertSee('Camiseta')->assertDontSee('Zerado');
    }

    public function test_sales_report_totals_and_ticket(): void
    {
        $this->product->registerMovement('saida', 2, 'venda');  // 200
        $this->product->registerMovement('saida', 1, 'venda');  // 100
        $this->product->registerMovement('saida', 1, 'perda');  // não conta como faturamento
        $this->product->registerMovement('saida', 1, 'venda', null, null, null, now()->subMonths(2)); // fora do período

        $summary = \App\Services\SalesReport::fromRequest(request()->merge(['period' => 'month']))->summary();

        $this->assertSame(300.0, $summary['total']);
        $this->assertSame(3, $summary['units']);
        $this->assertSame(2, $summary['count']);
        $this->assertSame(150.0, $summary['average_ticket']);
        $this->assertSame(120.0, $summary['cost']);   // 3 un. x 40
        $this->assertSame(180.0, $summary['profit']);
        $this->assertSame(60.0, $summary['margin']);
    }

    public function test_profit_uses_cost_at_time_of_sale(): void
    {
        $this->product->registerMovement('saida', 1, 'venda'); // custo 40
        $this->product->update(['cost_price' => 70]);
        $this->product->registerMovement('saida', 1, 'venda'); // custo 70

        $summary = \App\Services\SalesReport::fromRequest(request()->merge(['period' => 'month']))->summary();

        $this->assertSame(110.0, $summary['cost']);
        $this->assertSame(90.0, $summary['profit']);
    }

    public function test_pages_and_exports_render(): void
    {
        $this->product->registerMovement('saida', 2, 'venda');
        $this->actingAs($this->user);

        foreach (['/dashboard', '/dashboard?period=day', '/dashboard?period=week', '/produtos', '/produtos/'.$this->product->id,
            '/produtos/create', '/produtos/'.$this->product->id.'/edit', '/categorias', '/movimentacoes',
            '/movimentacoes/nova?type=saida', '/relatorios/faturamento', '/relatorios/faturamento?period=day',
            '/relatorios/faturamento?period=week', '/relatorios/faturamento?period=custom&from=2026-01-01&to=2026-12-31'] as $url) {
            $this->get($url)->assertOk();
        }

        $this->get('/relatorios/faturamento/pdf?period=month')->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->get('/relatorios/faturamento/excel?period=month')->assertOk();
        $this->get('/relatorios/estoque/excel')->assertOk();
    }

    public function test_deleting_product_keeps_sales_history(): void
    {
        $this->product->registerMovement('saida', 1, 'venda');
        $this->actingAs($this->user)->delete("/produtos/{$this->product->id}")->assertRedirect('/produtos');

        $this->assertSoftDeleted($this->product);
        $this->assertSame(1, StockMovement::count());
        $this->actingAs($this->user)->get('/movimentacoes')->assertOk()->assertSee('Camiseta');
        $this->actingAs($this->user)->get('/relatorios/faturamento/pdf')->assertOk();
    }
}
