<?php

namespace App\Http\Resources\Projects;

use App\Models\Projects\Budget;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
{

    public static function statusName($status)
    {
        $name = "Draft";
        switch ($status) {
            case Budget::STATUS_APPROVED:
                $name = "Approved";
                break;
            case Budget::STATUS_REJECTED:
                $name = "Rejected";
                break;
        }
        return [
            "value" => intval($status),
            "display" => $name
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
            'status' => self::statusName($this->status),
            'name' => $this->name,
            'gainMargin' => $this->gainMargin,
            'projectNumber' => $this->projectNumber,
            'projectLocation' => $this->projectLocation,
            'totalPowerPick' => $this->totalPowerPick,
            'updatedAt' => $this->updatedAt->toIso8601String()
        ];
    }
}
