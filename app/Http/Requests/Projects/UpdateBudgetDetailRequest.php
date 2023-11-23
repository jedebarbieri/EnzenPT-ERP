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
            'unit_price' => 'numeric|nullable',
            'sell_price' => 'numeric|nullable',
            'quantity' => 'numeric|nullable',
            'tax_percentage' => 'numeric|gte:0|lte:1|nullable',
            'discount' => 'numeric|nullable',
        ];
    }
}
