<?php

namespace App\Http\Requests\Harvest;

use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;

class StoreHarvestRequest extends FormRequest
{
    use ExtractsRequestContext;

    public function authorize(): bool
    {
        return $this->hasPermission($this, 'create_harvests');
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['nullable', 'uuid'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'total_quantity' => ['required', 'numeric', 'min:0.01'],
            'unit' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
