<?php

namespace App\Http\Requests\Crop;

use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;

class StoreCropRequest extends FormRequest
{
    use ExtractsRequestContext;

    public function authorize(): bool
    {
        return $this->hasPermission($this, 'create_crops');
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:crops,name'],
            'description' => ['nullable', 'string'],
            'unit' => ['nullable', 'string', 'max:20'],
        ];
    }
}
