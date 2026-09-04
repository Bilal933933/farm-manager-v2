<?php

namespace App\Http\Resources;

use App\Models\Harvest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Harvest
 */
class HarvestResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'season_id' => $this->season_id,
            'product_id' => $this->product_id,
            'date' => $this->date,
            'total_quantity' => $this->total_quantity,
            'unit' => $this->unit,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'season' => new SeasonResource($this->whenLoaded('season')),
        ];
    }
}
