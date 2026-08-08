<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'reference' => ['required', 'string', 'max:50', 'unique:products,reference'],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:products,barcode'],
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'brand' => ['nullable', 'string', 'max:100'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'supplier_sale_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_active' => ['boolean'],
            'tracks_imei' => ['boolean'],

            // ── Boutique en ligne ──────────────────────────────
            'show_on_store' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_new' => ['boolean'],
            'is_promo' => ['boolean'],
            'promo_price' => ['nullable', 'numeric', 'min:0', 'lt:sale_price'],
            'allow_order' => ['boolean'],
            'show_stock' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:191'],
            'meta_description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'La catégorie est obligatoire.',
            'reference.required' => 'La référence est obligatoire.',
            'reference.unique' => 'Cette référence existe déjà.',
            'name.required' => 'Le nom du produit est obligatoire.',
            'sale_price.required' => 'Le prix de vente est obligatoire.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'tracks_imei' => $this->boolean('tracks_imei'),
            // Le stock d'un produit suivi par IMEI est toujours recalculé à
            // partir des IMEI enregistrés, jamais saisi manuellement.
            'stock_quantity' => $this->boolean('tracks_imei') ? 0 : $this->input('stock_quantity'),
            'show_on_store' => $this->boolean('show_on_store'),
            'is_featured' => $this->boolean('is_featured'),
            'is_new' => $this->boolean('is_new'),
            'is_promo' => $this->boolean('is_promo'),
            'allow_order' => $this->boolean('allow_order'),
            'show_stock' => $this->boolean('show_stock'),
        ]);
    }
}
