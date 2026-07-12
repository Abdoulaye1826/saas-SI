<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateEntrepriseRequest;
use App\Models\Entreprise;
use Illuminate\Support\Facades\Storage;

class EntrepriseController extends Controller
{
    public function edit()
    {
        $entreprise = Entreprise::current();

        return view('admin.entreprise.edit', compact('entreprise'));
    }

    public function update(UpdateEntrepriseRequest $request)
    {
        $entreprise = Entreprise::current();
        $data = $request->validated();
        unset($data['logo']);

        if ($request->hasFile('logo')) {
            if ($entreprise->logo_path) {
                Storage::disk('public')->delete($entreprise->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('entreprise', 'public');
        }

        $entreprise->update($data);

        return back()->with('success', "Informations de l'entreprise mises à jour.");
    }
}
