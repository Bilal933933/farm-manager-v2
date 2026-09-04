<?php

namespace App\Http\Requests\Land;

use App\Enums\AreaUnit;
use App\Enums\LandStatus;
use App\Enums\OwnershipType;
use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLandRequest extends FormRequest
{
    use ExtractsRequestContext;

    public function authorize(): bool
    {
        return $this->hasPermission($this, 'create_lands');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:150', 'unique:lands,slug'],
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'description' => ['nullable', 'string'],
            'area' => ['required', 'numeric', 'min:0.01'],
            'area_unit' => ['sometimes', Rule::enum(AreaUnit::class)],
            'map_coordinates' => ['nullable', 'array'],
            'ownership_type' => ['required', Rule::enum(OwnershipType::class)],
            'owner_party_id' => ['required', 'uuid'],
            'status' => ['sometimes', Rule::enum(LandStatus::class)],
        ];
    }
}
