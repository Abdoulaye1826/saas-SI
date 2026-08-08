<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStorePageRequest;
use App\Models\StorePage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StorePageController extends Controller
{
    public function index(): View
    {
        $pages = StorePage::orderBy('title')->paginate(15);

        return view('admin.store-pages.index', compact('pages'));
    }

    public function create(): View
    {
        return view('admin.store-pages.create');
    }

    public function store(StoreStorePageRequest $request): RedirectResponse
    {
        StorePage::create($request->validated());

        return redirect()->route('store-pages.index')->with('success', 'Page créée avec succès.');
    }

    public function edit(StorePage $storePage): View
    {
        return view('admin.store-pages.edit', ['page' => $storePage]);
    }

    public function update(StoreStorePageRequest $request, StorePage $storePage): RedirectResponse
    {
        $storePage->update($request->validated());

        return redirect()->route('store-pages.index')->with('success', 'Page mise à jour avec succès.');
    }

    public function destroy(StorePage $storePage): RedirectResponse
    {
        $storePage->delete();

        return redirect()->route('store-pages.index')->with('success', 'Page supprimée avec succès.');
    }
}
