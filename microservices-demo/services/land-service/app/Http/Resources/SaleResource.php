<?php

namespace App\Http\Resources;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Sale
 */
class SaleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'season_id' => $this->season_id,
            'harvest_id' => $this->harvest_id,
            'product_id' => $this->product_id,
            'buyer_party_id' => $this->buyer_party_id,
            'buyer_name' => $this->buyer_name,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
            'discount_amount' => $this->discount_amount,
            'tax_amount' => $this->tax_amount,
            'delivery_cost' => $this->delivery_cost,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'date' => $this->date,
            'due_date' => $this->due_date,
            'payment_status' => $this->payment_status,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'season' => new SeasonResource($this->whenLoaded('season')),
            'harvest' => new HarvestResource($this->whenLoaded('harvest')),
        ];
    }
}
