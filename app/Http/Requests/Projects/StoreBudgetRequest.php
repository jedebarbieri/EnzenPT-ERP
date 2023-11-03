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
            'total_peak_power' => $this->input('total_peak_power', 0),
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
            'name' => 'string|min:3',
            'total_peak_power' => 'numeric|gte:0',
            'gain_margin' => 'numeric|gte:0',
            'project_name' => 'string|min:3',
            'project_number' => 'string|min:3',
            'project_location' => 'string|min:3',
            'status' => 'integer|in:' . implode(',', Budget::STATUS)
        ];
    }
}
