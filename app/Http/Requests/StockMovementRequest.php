<?php

namespace App\Http\Requests;

use App\Models\StockMovement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $type = $this->input('type');

        return [
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', Rule::in([StockMovement::TYPE_IN, StockMovement::TYPE_OUT])],
            'reason' => ['required', Rule::in(array_keys(StockMovement::REASONS[$type] ?? []))],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000000'],
            'unit_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('unit_price'))) {
            $this->merge(['unit_price' => str_replace(',', '.', $this->input('unit_price')) ?: null]);
        }
    }
}
