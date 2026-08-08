<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\StoreEvent;
use App\Services\StoreAnalyticsService;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private readonly StoreAnalyticsService $analytics)
    {
    }

    public function show(Category $category): View
    {
        abort_unless($category->show_on_store && $category->is_active, 404);

        $this->analytics->track(StoreEvent::TYPE_PAGE_VIEW);

        $products = $category->products()
            ->onStore()
            ->orderByDesc('created_at')
            ->paginate(12);

        $categories = Category::onStore()->get();

        return view('storefront.categories.show', compact('category', 'products', 'categories'));
    }
}
