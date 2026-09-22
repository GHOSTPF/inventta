<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockMovementRequest;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $movements = StockMovement::query()
            ->with(['product', 'user'])
            ->when($request->filled('product'), fn ($q) => $q->where('product_id', $request->product))
            ->when(in_array($request->type, [StockMovement::TYPE_IN, StockMovement::TYPE_OUT], true),
                fn ($q) => $q->where('type', $request->type))
            ->latest()
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('movements.index', [
            'movements' => $movements,
            'products' => Product::orderBy('name')->get(['id', 'name', 'sku']),
        ]);
    }

    public function create(Request $request)
    {
        return view('movements.create', [
            'products' => Product::orderBy('name')->get(['id', 'name', 'sku', 'quantity', 'cost_price', 'sale_price']),
            'selected' => $request->query('product'),
            'type' => $request->query('type') === StockMovement::TYPE_OUT ? StockMovement::TYPE_OUT : StockMovement::TYPE_IN,
        ]);
    }

    public function store(StockMovementRequest $request)
    {
        $data = $request->validated();

        $product = Product::findOrFail($data['product_id']);
        $product->registerMovement(
            $data['type'],
            (int) $data['quantity'],
            $data['reason'],
            isset($data['unit_price']) ? (float) $data['unit_price'] : null,
            $data['notes'] ?? null,
            $request->user()->id,
        );

        return redirect()->route('movements.index')
            ->with('status', "Movimentação registrada. Estoque de {$product->name}: {$product->quantity}.");
    }
}
