<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VerifyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'valid' => true,
            'user_id' => $this->resource->id,
            'company_id' => $this->resource->company_id,
            'role' => $this->resource->role?->slug,
            'permissions' => $this->resource->role?->permissions->pluck('name') ?? [],
        ];
    }
}
