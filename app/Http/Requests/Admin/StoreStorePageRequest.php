<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class StoreStorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:191'],
            'slug' => ['nullable', 'string', 'max:100', 'alpha_dash', Rule::unique('store_pages', 'slug')->ignore($this->route('storePage'))],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:191'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'is_published' => ['boolean'],
            'show_in_footer' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
            'show_in_footer' => $this->boolean('show_in_footer'),
            'slug' => $this->filled('slug') ? Str::slug($this->input('slug')) : Str::slug($this->input('title')),
        ]);
    }
}
