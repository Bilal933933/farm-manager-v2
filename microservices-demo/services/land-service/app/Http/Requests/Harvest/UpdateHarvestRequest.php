<?php

namespace App\Http\Requests\Harvest;

use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHarvestRequest extends FormRequest
{
    use ExtractsRequestContext;

    public function authorize(): bool
    {
        return $this->hasPermission($this, 'update_harvests');
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['nullable', 'uuid'],
            'date' => ['sometimes', 'date', 'before_or_equal:today'],
            'total_quantity' => ['sometimes', 'numeric', 'min:0.01'],
            'unit' => ['sometimes', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
