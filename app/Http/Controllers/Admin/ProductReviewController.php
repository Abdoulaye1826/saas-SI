<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductReviewController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['status']);

        $reviews = ProductReview::query()
            ->with(['product', 'customer'])
            ->status($filters['status'] ?? null)
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.product-reviews.index', compact('reviews', 'filters'));
    }

    public function approve(ProductReview $productReview): RedirectResponse
    {
        $productReview->update(['status' => 'approved']);

        return back()->with('success', 'Avis validé.');
    }

    public function reject(ProductReview $productReview): RedirectResponse
    {
        $productReview->update(['status' => 'rejected']);

        return back()->with('success', 'Avis refusé.');
    }

    public function hide(ProductReview $productReview): RedirectResponse
    {
        $productReview->update(['status' => 'hidden']);

        return back()->with('success', 'Avis masqué.');
    }
}
