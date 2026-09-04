<?php

namespace App\Http\Requests\Land;

use App\Enums\AreaUnit;
use App\Enums\LandStatus;
use App\Enums\OwnershipType;
use App\Models\Land;
use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLandRequest extends FormRequest
{
    use ExtractsRequestContext;

    public function authorize(): bool
    {
        return $this->hasPermission($this, 'update_lands');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $landId = $this->route('land') instanceof Land
            ? $this->route('land')->id
            : $this->route('land');

        return [
            'slug' => ['sometimes', 'string', 'max:150', Rule::unique('lands', 'slug')->ignore($landId)],
            'name' => ['sometimes', 'string', 'min:3', 'max:150'],
            'description' => ['nullable', 'string'],
            'area' => ['sometimes', 'numeric', 'min:0.01'],
            'area_unit' => ['sometimes', Rule::enum(AreaUnit::class)],
            'map_coordinates' => ['nullable', 'array'],
            'ownership_type' => ['sometimes', Rule::enum(OwnershipType::class)],
            'owner_party_id' => ['sometimes', 'uuid'],
            'status' => ['sometimes', Rule::enum(LandStatus::class)],
        ];
    }
}
