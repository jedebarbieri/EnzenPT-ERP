<?php

namespace App\Http\Resources\Procurement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'internalCod' => $this->internal_cod,
            'unitPrice' => $this->unit_price,
            'itemCategory' => new ItemCategoryResource($this->itemCategory)
        ];
    }
}
