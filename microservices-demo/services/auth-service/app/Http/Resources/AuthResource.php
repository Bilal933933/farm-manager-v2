<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    // $this->resource = ['company'=>Company, 'user'=>User, 'role'=>Role]
    public function toArray($request): array
    {
        return [
            'company' => new CompanyResource($this->resource['company']),
            'user' => new UserResource($this->resource['user']),
            'role' => $this->resource['role']->name ?? $this->resource['role'],
        ];
    }

    public function with($request): array
    {
        return ['success' => true];
    }
}
