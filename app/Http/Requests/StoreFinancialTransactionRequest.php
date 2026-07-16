<?php

namespace App\Http\Requests;

use App\Enums\FinancialCategory;
use App\Enums\FinancialTransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreFinancialTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'financial_account_id' => ['required', 'exists:financial_accounts,id'],
            'type' => ['required', Rule::in(['in', 'out'])],
            'category' => ['required', Rule::in(array_column(FinancialCategory::cases(), 'value'))],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $type = $this->input('type');
            $categoryValue = $this->input('category');

            if (! $type || ! $categoryValue) {
                return;
            }

            $category = FinancialCategory::tryFrom($categoryValue);
            $transactionType = FinancialTransactionType::tryFrom($type);

            if ($category && $transactionType && ! in_array($transactionType, $category->allowedTypes(), true)) {
                $validator->errors()->add('category', 'Cette catégorie ne correspond pas au type sélectionné (entrée/sortie).');
            }
        });
    }
}
