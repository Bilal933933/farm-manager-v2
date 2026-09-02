<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->when(! is_null($this->phone), $this->phone),
            'company_id' => $this->company_id,
            'role' => $this->whenLoaded('role', fn () => $this->role?->name),
            'is_active' => $this->when($request->user()?->can('system.view'), $this->is_active),
        ];
    }
}
