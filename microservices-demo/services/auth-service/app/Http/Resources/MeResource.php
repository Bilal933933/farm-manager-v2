<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'user' => new UserResource($this->resource),
            'company' => $this->resource->company ? new CompanyResource($this->resource->company) : null,
            'role' => $this->resource->role ? [
                'name' => $this->resource->role->name,
                'slug' => $this->resource->role->slug,
                'permissions' => $this->resource->role->permissions->pluck('name'),
            ] : null,
        ];
    }

    public function with($request): array
    {
        return ['success' => true];
    }
}
