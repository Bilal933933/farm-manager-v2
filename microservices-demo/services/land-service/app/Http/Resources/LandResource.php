<?php

namespace App\Http\Resources;

use App\Models\Land;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Land
 */
class LandResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'area' => $this->area,
            'area_unit' => $this->area_unit,
            'map_coordinates' => $this->map_coordinates,
            'ownership_type' => $this->ownership_type,
            'owner_party_id' => $this->owner_party_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
