<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\StorePage;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(StorePage $page): View
    {
        abort_unless($page->is_published, 404);

        return view('storefront.pages.show', compact('page'));
    }
}
