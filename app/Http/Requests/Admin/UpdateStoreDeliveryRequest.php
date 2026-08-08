<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'delivery_enabled' => ['boolean'],
            'pickup_enabled' => ['boolean'],
            'delivery_fee_dakar' => ['required', 'numeric', 'min:0'],
            'delivery_fee_other' => ['required', 'numeric', 'min:0'],
            'free_delivery_threshold' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'delivery_enabled' => $this->boolean('delivery_enabled'),
            'pickup_enabled' => $this->boolean('pickup_enabled'),
        ]);
    }
}
