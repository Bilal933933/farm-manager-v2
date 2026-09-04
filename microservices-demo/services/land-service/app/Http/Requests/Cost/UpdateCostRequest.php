<?php

namespace App\Http\Requests\Cost;

use App\Enums\CostType;
use App\Enums\PaymentStatus;
use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCostRequest extends FormRequest
{
    use ExtractsRequestContext;

    public function authorize(): bool
    {
        return $this->hasPermission($this, 'update_costs');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'cost_type' => ['sometimes', Rule::enum(CostType::class)],
            'product_id' => ['nullable', 'uuid'],
            'quantity' => ['nullable', 'numeric', 'min:0.01'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'date' => ['sometimes', 'date'],
            'description' => ['nullable', 'string'],
            'payment_status' => ['sometimes', Rule::enum(PaymentStatus::class)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
