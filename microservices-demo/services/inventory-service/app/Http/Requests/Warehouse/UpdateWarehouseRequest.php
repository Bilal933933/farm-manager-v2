<?php

namespace App\Http\Requests\Warehouse;

use App\Enums\WarehouseStatus;
use App\Models\Warehouse;
use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWarehouseRequest extends FormRequest
{
    use ExtractsRequestContext;

    public function authorize(): bool
    {
        return $this->hasPermission($this, 'update_warehouses');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $warehouse = $this->route('warehouse');
        $warehouseId = $warehouse instanceof Warehouse ? $warehouse->id : $warehouse;
        $companyId = $this->attributes->get('company_id');

        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('warehouses', 'name')->where('company_id', $companyId)->ignore($warehouseId)],
            'location' => ['nullable', 'string'],
            'status' => ['sometimes', Rule::enum(WarehouseStatus::class)],
        ];
    }
}
