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
            'id' => 'required|numeric',
            'name' => 'string',
            'status' => 'numeric|gte:0',
            'total_peak_power' => 'numeric|gte:0',
            'gain_margin' => 'numeric|gte:0',
            'project_name' => 'string|min:3',
            'project_number' => 'string|min:3',
            'project_location' => 'string|min:3',
        ];
    }
}
