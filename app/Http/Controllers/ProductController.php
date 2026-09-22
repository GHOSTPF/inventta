<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with('category')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.mb_strtolower($request->q).'%';
                $query->where(fn ($q) => $q
                    ->whereRaw('LOWER(name) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(sku) LIKE ?', [$term]));
            })
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->category))
            ->when($request->status === 'ok', fn ($q) => $q->whereColumn('quantity', '>', 'min_quantity'))
            ->when($request->status === 'baixo', fn ($q) => $q->lowStock())
            ->when($request->status === 'critico', fn ($q) => $q->outOfStock())
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('products.create', [
            'product' => new Product(['quantity' => 0, 'min_quantity' => 5]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $initial = $data['quantity'];
        unset($data['image'], $data['remove_image']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([...$data, 'quantity' => 0]);

        // Estoque inicial entra como movimentação para o histórico bater com a quantidade.
        if ($initial > 0) {
            $product->registerMovement(
                StockMovement::TYPE_IN, $initial, 'ajuste', null, 'Estoque inicial', $request->user()->id,
            );
        }

        return redirect()->route('products.show', $product)->with('status', 'Produto cadastrado.');
    }

    public function show(Product $product)
    {
        return view('products.show', [
            'product' => $product->load('category'),
            'movements' => $product->movements()->with('user')->latest()->latest('id')->paginate(15),
        ]);
    }

    public function edit(Product $product)
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $newQuantity = $data['quantity'];
        unset($data['quantity'], $data['image'], $data['remove_image']);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        } elseif ($request->boolean('remove_image') && $product->image_path) {
            Storage::disk('public')->delete($product->image_path);
            $data['image_path'] = null;
        }

        $product->update($data);

        // Alterar a quantidade manualmente vira um ajuste no histórico.
        $diff = $newQuantity - $product->quantity;
        if ($diff !== 0) {
            $product->registerMovement(
                $diff > 0 ? StockMovement::TYPE_IN : StockMovement::TYPE_OUT,
                abs($diff), 'ajuste', null, 'Ajuste manual na edição do produto', $request->user()->id,
            );
        }

        return redirect()->route('products.show', $product)->with('status', 'Produto atualizado.');
    }

    public function destroy(Product $product)
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('products.index')->with('status', 'Produto removido.');
    }
}
