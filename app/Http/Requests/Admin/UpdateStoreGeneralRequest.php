<?php

namespace App\Http\Requests\Admin;

use App\Enums\StoreStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStoreGeneralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_column(StoreStatus::cases(), 'value'))],
            'name' => ['nullable', 'string', 'max:150'],
            'slogan' => ['nullable', 'string', 'max:191'],
            'description' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:191'],
            'opening_hours' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_logo' => ['boolean'],
            'favicon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,ico', 'max:1024'],
            'remove_favicon' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'remove_logo' => $this->boolean('remove_logo'),
            'remove_favicon' => $this->boolean('remove_favicon'),
        ]);
    }

    /**
     * `opening_hours` est saisi comme un texte libre multi-lignes dans le
     * formulaire (une ligne par jour) mais stocké en JSON (un tableau de
     * lignes) — plus simple à afficher/éditer côté commerçant qu'une grille
     * de champs par jour, tout en restant structuré côté modèle.
     */
    public function openingHoursLines(): ?array
    {
        $raw = $this->validated('opening_hours');

        if (empty($raw)) {
            return null;
        }

        return collect(preg_split('/\r\n|\r|\n/', $raw))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
