<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return view('categories.index', [
            'categories' => Category::withCount('products')->orderBy('name')->get(),
        ]);
    }

    public function store(CategoryRequest $request)
    {
        Category::create($request->validated());

        return back()->with('status', 'Categoria criada.');
    }

    public function destroy(Category $category)
    {
        $category->delete(); // produtos ficam sem categoria (nullOnDelete)

        return back()->with('status', 'Categoria removida.');
    }
}
