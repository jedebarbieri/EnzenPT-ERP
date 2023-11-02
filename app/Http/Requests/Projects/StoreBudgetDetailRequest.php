<?php

namespace App\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'item_id' => 'required|integer',
            'budget_id' => 'required|integer',
            'unit_price' => 'numeric',
            'sell_price' => 'numeric',
            'quantity' => 'integer',
            'tax_percentage' => 'numeric',
            'discount' => 'numeric',
        ];
    }
}
