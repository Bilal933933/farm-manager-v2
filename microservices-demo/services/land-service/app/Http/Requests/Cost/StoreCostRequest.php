<?php

namespace App\Http\Requests\Cost;

use App\Enums\CostType;
use App\Enums\PaymentStatus;
use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCostRequest extends FormRequest
{
    use ExtractsRequestContext;

    public function authorize(): bool
    {
        return $this->hasPermission($this, 'create_costs');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'cost_type' => ['required', Rule::enum(CostType::class)],
            'product_id' => ['nullable', 'uuid'],
            'quantity' => ['required_with:product_id', 'nullable', 'numeric', 'min:0.01'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'amount' => ['required_without:product_id', 'nullable', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'payment_status' => ['sometimes', Rule::enum(PaymentStatus::class)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
