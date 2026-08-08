<?php

namespace App\Http\Requests\Storefront;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150', Rule::unique('customers', 'email')],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Votre nom est obligatoire.',
            'phone.required' => 'Votre numéro de téléphone est obligatoire.',
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre compte.',
        ];
    }
}
