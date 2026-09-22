<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;

/** Categorias, produtos e ~30 dias de movimentações de exemplo. */
class InventorySeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(2026); // dados de exemplo reproduzíveis

        $userId = User::value('id');

        // categoria => [[nome, sku, custo, venda, estoque mínimo], ...]
        $catalog = [
            'Vestuário' => [
                ['Camiseta Premium', 'VES-001', 39.90, 89.90, 10],
                ['Calça Jeans Slim', 'VES-002', 79.00, 189.90, 8],
                ['Jaqueta Corta-vento', 'VES-003', 110.00, 259.90, 5],
                ['Boné Aba Curva', 'VES-004', 18.50, 49.90, 12],
            ],
            'Calçados' => [
                ['Tênis Runner', 'CAL-001', 140.00, 329.90, 6],
                ['Chinelo Slide', 'CAL-002', 12.00, 39.90, 15],
                ['Bota Urbana', 'CAL-003', 160.00, 379.90, 4],
            ],
            'Acessórios' => [
                ['Mochila 25L', 'ACE-001', 65.00, 149.90, 6],
                ['Garrafa Térmica 500ml', 'ACE-002', 22.00, 59.90, 10],
                ['Carteira Couro', 'ACE-003', 35.00, 99.90, 8],
            ],
            'Eletrônicos' => [
                ['Fone Bluetooth', 'ELE-001', 55.00, 159.90, 8],
                ['Carregador Turbo 20W', 'ELE-002', 24.00, 69.90, 10],
                ['Smartwatch Fit', 'ELE-003', 180.00, 449.90, 4],
            ],
        ];

        $start = now()->subDays(30)->setTime(9, 0);

        foreach ($catalog as $categoryName => $items) {
            $category = Category::firstOrCreate(['name' => $categoryName]);

            foreach ($items as [$name, $sku, $cost, $price, $min]) {
                $product = Product::create([
                    'name' => $name, 'sku' => $sku, 'category_id' => $category->id,
                    'cost_price' => $cost, 'sale_price' => $price, 'quantity' => 0, 'min_quantity' => $min,
                ]);

                $product->registerMovement(
                    StockMovement::TYPE_IN, mt_rand(40, 90), 'compra', null, 'Compra inicial', $userId, $start,
                );

                for ($day = 29; $day >= 0; $day--) {
                    if (mt_rand(1, 100) > 55) {
                        continue;
                    }

                    $at = now()->subDays($day)->setTime(mt_rand(9, 19), mt_rand(0, 59));
                    $qty = min(mt_rand(1, 4), $product->quantity);

                    if ($qty > 0) {
                        $product->registerMovement(StockMovement::TYPE_OUT, $qty, 'venda', null, null, $userId, $at);
                    }
                }

                if (mt_rand(1, 100) <= 15 && $product->quantity > 0) {
                    $product->registerMovement(StockMovement::TYPE_OUT, 1, 'perda', null, 'Avaria no transporte', $userId, now()->subDays(mt_rand(1, 10)));
                }
            }
        }

        // Deixa alguns itens propositalmente baixos/críticos para o dashboard.
        foreach (['Tênis Runner' => 5, 'Camiseta Premium' => 4, 'Smartwatch Fit' => 0] as $name => $target) {
            $product = Product::where('name', $name)->first();
            if ($product && $product->quantity > $target) {
                $product->registerMovement(
                    StockMovement::TYPE_OUT, $product->quantity - $target, 'ajuste', null, 'Contagem de inventário', $userId, now()->subHour(),
                );
            }
        }
    }
}
