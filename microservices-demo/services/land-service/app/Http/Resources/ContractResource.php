<?php

namespace App\Http\Resources;

use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Contract
 */
class ContractResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'land_id' => $this->land_id,
            'contract_type' => $this->contract_type,
            'counterparty_party_id' => $this->counterparty_party_id,
            'owner_party_id' => $this->owner_party_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'financial_value' => $this->financial_value,
            'revenue_share_percentage' => $this->revenue_share_percentage,
            'payment_terms' => $this->payment_terms,
            'notes' => $this->notes,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'land' => new LandResource($this->whenLoaded('land')),
        ];
    }
}
