<?php

namespace App\Http\Requests\Projects;

use App\Models\Projects\Budget;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBudgetRequest extends FormRequest
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
            'name' => 'string|nullable|min:3',
            'status' => 'numeric|nullable|in:' . implode(',', array_keys(Budget::STATUS)),
            'total_peak_power' => 'numeric|nullable|gte:0',
            'gain_margin' => 'numeric|gte:0|lte:1|nullable',
            'project_name' => 'string|nullable|min:3',
            'project_number' => 'string|nullable|min:3',
            'project_location' => 'string|nullable|min:3',
        ];
    }
}
