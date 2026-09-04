<?php

namespace App\Http\Requests\Warehouse;

use App\Enums\WarehouseStatus;
use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWarehouseRequest extends FormRequest
{
    use ExtractsRequestContext;

    public function authorize(): bool
    {
        return $this->hasPermission($this, 'create_warehouses');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $companyId = $this->attributes->get('company_id');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('warehouses', 'name')->where('company_id', $companyId)],
            'location' => ['nullable', 'string'],
            'status' => ['sometimes', Rule::enum(WarehouseStatus::class)],
        ];
    }
}
