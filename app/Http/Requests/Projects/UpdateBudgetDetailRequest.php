<?php

namespace App\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBudgetDetailRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
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
            'id' => 'required|numeric',
            'unit_price' => 'numeric|gte:0',
            'quantity' => 'numeric|gte:0',
            'tax_percentage' => 'numeric|gte:0|lte:1',
            'discount' => 'numeric',
            'sell_price' => 'numeric',
        ];
    }
}
