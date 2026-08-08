<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStoreMenuRequest;
use App\Models\StoreMenu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreMenuController extends Controller
{
    public function index(): View
    {
        $menus = StoreMenu::withoutGlobalScopes()->orderBy('sort_order')->get();

        return view('admin.store-menus.index', compact('menus'));
    }

    public function create(): View
    {
        return view('admin.store-menus.create');
    }

    public function store(StoreStoreMenuRequest $request): RedirectResponse
    {
        StoreMenu::create($request->validated());

        return redirect()->route('store-menus.index')->with('success', 'Élément de menu créé avec succès.');
    }

    public function edit(StoreMenu $storeMenu): View
    {
        return view('admin.store-menus.edit', ['menu' => $storeMenu]);
    }

    public function update(StoreStoreMenuRequest $request, StoreMenu $storeMenu): RedirectResponse
    {
        $storeMenu->update($request->validated());

        return redirect()->route('store-menus.index')->with('success', 'Élément de menu mis à jour avec succès.');
    }

    public function destroy(StoreMenu $storeMenu): RedirectResponse
    {
        $storeMenu->delete();

        return redirect()->route('store-menus.index')->with('success', 'Élément de menu supprimé avec succès.');
    }

    /**
     * Met à jour l'ordre de tous les éléments en une seule action, depuis
     * un champ "ordre" numérique par ligne dans la liste (pas de
     * glisser-déposer — aucune librairie de ce type dans le projet).
     */
    public function reorder(Request $request): RedirectResponse
    {
        $orders = $request->input('order', []);

        foreach ($orders as $id => $sortOrder) {
            StoreMenu::withoutGlobalScopes()->where('id', $id)->update(['sort_order' => (int) $sortOrder]);
        }

        return redirect()->route('store-menus.index')->with('success', 'Ordre du menu mis à jour.');
    }
}
