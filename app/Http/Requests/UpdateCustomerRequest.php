<?php

namespace App\Http\Requests;

use App\Enums\CustomerType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')->id ?? null;

        return [
            'full_name' => ['required', 'string', 'max:150'],
            'type' => ['nullable', Rule::in(array_column(CustomerType::cases(), 'value'))],
            'phone' => ['required', 'string', 'max:20'],
            'email' => [
                'nullable',
                'email',
                'max:150',
                Rule::unique('customers', 'email')->ignore($customerId),
            ],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'registered_at' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Le nom complet du client est obligatoire.',
            'phone.required' => 'Le téléphone du client est obligatoire.',
            'email.email' => 'L’adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'registered_at.required' => 'La date d’inscription est obligatoire.',
            'registered_at.date' => 'La date d’inscription doit être valide.',
        ];
    }
}
