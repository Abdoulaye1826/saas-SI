<?php

namespace App\Http\Requests\Storefront;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guest_name' => ['required', 'string', 'max:150'],
            'guest_phone' => ['required', 'string', 'max:50'],
            'guest_email' => ['nullable', 'email', 'max:150'],
            'delivery_method' => ['required', 'in:home,pickup'],
            'delivery_address' => ['required_if:delivery_method,home', 'nullable', 'string', 'max:255'],
            'delivery_city' => ['required_if:delivery_method,home', 'nullable', 'string', 'max:100'],
            'delivery_zone' => ['required_if:delivery_method,home', 'nullable', 'in:dakar,other'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'guest_name.required' => 'Votre nom est obligatoire.',
            'guest_phone.required' => 'Votre numéro de téléphone est obligatoire.',
            'delivery_address.required_if' => "L'adresse de livraison est obligatoire pour une livraison à domicile.",
            'delivery_city.required_if' => 'La ville est obligatoire pour une livraison à domicile.',
            'delivery_zone.required_if' => 'Merci de préciser la zone de livraison.',
        ];
    }
}
