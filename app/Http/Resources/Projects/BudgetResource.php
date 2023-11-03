<?php

namespace App\Http\Resources\Projects;

use App\Models\Projects\Budget;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
{

    /**
     * Return an array with the status name and value
     * @param int $status
     */
    public static function statusName($status)
    {
        return [
            "value" => intval($status),
            "display" => Budget::STATUS[$status],
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'budgetDetails' => BudgetDetailsResource::collection($this->budgetDetails),
            'status' => [
                "value" => intval($this->status),
                "display" => Budget::STATUS[$this->status],
            ],
            'name' => $this->name,
            'gainMargin' => $this->gain_margin,
            'projectName' => $this->project_name,
            'projectNumber' => $this->project_number,
            'projectLocation' => $this->project_location,
            'totalPeakPower' => $this->total_peak_power,
            'updatedAt' => $this->updated_at->toIso8601String()
        ];
    }
}
