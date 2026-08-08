<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateStoreAppearanceRequest;
use App\Http\Requests\Admin\UpdateStoreGeneralRequest;
use App\Models\OnlineStoreSettings;
use App\Services\OnlineStoreSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StoreSettingsController extends Controller
{
    public function __construct(
        private readonly OnlineStoreSettingsService $storeSettingsService
    ) {
    }

    public function editGeneral(): View
    {
        $settings = OnlineStoreSettings::current();

        return view('admin.store.general', compact('settings'));
    }

    public function updateGeneral(UpdateStoreGeneralRequest $request): RedirectResponse
    {
        $settings = OnlineStoreSettings::current();
        $data = $request->validated();
        $data['opening_hours'] = $request->openingHoursLines();

        $this->storeSettingsService->update($settings, $data, [
            'logo' => $request->file('logo'),
            'favicon' => $request->file('favicon'),
        ]);

        return back()->with('success', 'Réglages généraux de la boutique mis à jour.');
    }

    public function editAppearance(): View
    {
        $settings = OnlineStoreSettings::current();

        return view('admin.store.appearance', compact('settings'));
    }

    public function updateAppearance(UpdateStoreAppearanceRequest $request): RedirectResponse
    {
        $settings = OnlineStoreSettings::current();
        $data = $request->validated();

        $this->storeSettingsService->update($settings, $data, [
            'hero_image' => $request->file('hero_image'),
        ]);

        return back()->with('success', 'Apparence de la boutique mise à jour.');
    }
}
