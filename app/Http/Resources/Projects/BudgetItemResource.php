<?php

namespace App\Http\Resources\Projects;

use App\Http\Resources\Procurement\ItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetItemResource extends JsonResource
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
            'item' => new ItemResource($this->item),
            'unitPrice' => $this->unitPrice,
            'quantity' => $this->quantity,
            'taxPercentage' => $this->taxPercentage,
            'discount' => $this->discount,
            'sellPrice' => $this->sellPrice
        ];
    }
}