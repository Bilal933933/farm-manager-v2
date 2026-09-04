<?php

namespace App\Http\Requests\Crop;

use App\Models\Crop;
use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCropRequest extends FormRequest
{
    use ExtractsRequestContext;

    public function authorize(): bool
    {
        return $this->hasPermission($this, 'update_crops');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $crop = $this->route('crop');
        $cropId = $crop instanceof Crop ? $crop->id : $crop;

        return [
            'name' => ['sometimes', 'string', 'max:100', Rule::unique('crops', 'name')->ignore($cropId)],
            'description' => ['nullable', 'string'],
            'unit' => ['nullable', 'string', 'max:20'],
        ];
    }
}
