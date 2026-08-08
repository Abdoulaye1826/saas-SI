<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\OnlineStoreSettings;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->show_on_store && $product->is_active, 404);

        if (! OnlineStoreSettings::current()->reviews_enabled) {
            abort(404);
        }

        $customerId = Auth::guard('customer')->id();

        if (ProductReview::where('product_id', $product->id)->where('customer_id', $customerId)->exists()) {
            return back()->with('error', 'Vous avez déjà donné votre avis sur ce produit.');
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        ProductReview::create([
            'product_id' => $product->id,
            'customer_id' => $customerId,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Merci ! Votre avis a été envoyé et sera visible après validation.');
    }
}
