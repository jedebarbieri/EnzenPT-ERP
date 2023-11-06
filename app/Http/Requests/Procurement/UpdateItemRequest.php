<?php

namespace App\Http\Requests\Procurement;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
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
            'name' => 'string|nullable|min:3',
            'internal_cod' => 'string|nullable|min:5',
            'unit_price' => 'numeric|nullable|gte:0',
            'item_category_id' => 'required|numeric|gte:0'
        ];
    }
}
