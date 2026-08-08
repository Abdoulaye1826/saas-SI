<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(Category $category): View
    {
        abort_unless($category->show_on_store && $category->is_active, 404);

        $products = $category->products()
            ->onStore()
            ->orderByDesc('created_at')
            ->paginate(12);

        $categories = Category::onStore()->get();

        return view('storefront.categories.show', compact('category', 'products', 'categories'));
    }
}
