<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Contrairement à la vente (client facultatif) : un cadeau doit
            // toujours avoir un bénéficiaire identifié (cahier §2).
            'customer_id' => ['required', 'exists:customers,id'],
            'product_id' => ['required', 'array', 'min:1'],
            'product_id.*' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'array', 'min:1'],
            'quantity.*' => ['required', 'integer', 'min:1'],
            'imei' => ['nullable', 'array'],
            'imei.*' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
