<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreAppearanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $color = ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'];

        return [
            'primary_color' => $color,
            'secondary_color' => $color,
            'navbar_color' => $color,
            'button_color' => $color,
            'link_color' => $color,
            'footer_color' => $color,

            'hero_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_hero_image' => ['boolean'],
            'hero_title' => ['nullable', 'string', 'max:191'],
            'hero_subtitle' => ['nullable', 'string', 'max:255'],
            'hero_button_label' => ['nullable', 'string', 'max:100'],
            'hero_button_url' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'remove_hero_image' => $this->boolean('remove_hero_image'),
        ]);
    }
}
