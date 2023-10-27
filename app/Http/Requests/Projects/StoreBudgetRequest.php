<?php

namespace App\Http\Requests\Projects;

use App\Models\Projects\Budget;
use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Establecer valores por defecto si no se proporcionaron en la solicitud
        $this->merge([
            'total_power_pick' => $this->input('total_power_pick', 0),
            'gain_margin' => $this->input('gain_margin', 0),
            'status' => $this->input('status', Budget::STATUS_DRAFT)
        ]);
    }

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
            'name' => 'required|string',
            'total_power_pick' => 'numeric|gte:0',
            'gain_margin' => 'numeric|gte:0',
            'project_name' => 'required|string|min:3',
            'project_number' => 'required|string|min:3',
            'project_location' => 'required|string|min:3',
        ];
    }
}
