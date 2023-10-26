<?php

namespace App\Http\Resources\Projects;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'items' => empty($this->items) ? [] : BudgetItemResource::collection($this->items),
            'status' => $this->status,
            'name' => $this->name,
            'gainMargin' => $this->gainMargin,
            'projectNumber' => $this->projectNumber,
            'projectLocation' => $this->projectLocation,
            'totalPowerPick' => $this->totalPowerPick
        ];
    }
}
