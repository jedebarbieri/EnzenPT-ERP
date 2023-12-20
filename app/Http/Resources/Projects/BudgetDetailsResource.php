<?php

namespace App\Http\Resources\Projects;

use App\Http\Resources\Procurement\ItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetDetailsResource extends JsonResource
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
            'unitPrice' => $this->unit_price,
            'quantity' => $this->quantity,
            'taxPercentage' => $this->tax_percentage,
            'discount' => $this->discount,
            'sellPrice' => $this->sell_price,
            'totalWithoutTax' => $this->total_without_tax,
            'costAmount' => $this->cost_amount,
            'totalWithoutTaxAfterDiscount' => $this->total_without_tax_after_discount,
            'totalWithTax' => $this->total_with_tax,
            'taxAmount' => $this->tax_amount,
            'pricePerWp' => $this->price_per_wp,
            'costPerWp' => $this->cost_per_wp,
            'gainAmount' => $this->gain_amount,
            'gainMargin' => $this->gain_margin,
        ];
    }
}