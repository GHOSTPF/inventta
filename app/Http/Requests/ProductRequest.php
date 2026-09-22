<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'required', 'string', 'max:64',
                Rule::unique('products', 'sku')->ignore($this->route('product')),
            ],
            'category_id' => ['nullable', 'exists:categories,id'],
            'cost_price' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'sale_price' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'quantity' => ['required', 'integer', 'min:0', 'max:100000000'],
            'min_quantity' => ['required', 'integer', 'min:0', 'max:100000000'],
            'image' => ['nullable', 'image', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
        ];
    }

    /** Aceita vírgula decimal (12,50) nos campos de preço. */
    protected function prepareForValidation(): void
    {
        foreach (['cost_price', 'sale_price'] as $field) {
            if (is_string($this->input($field))) {
                $this->merge([$field => str_replace(',', '.', $this->input($field))]);
            }
        }
    }
}
