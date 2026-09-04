<?php

namespace App\Http\Requests\Cost;

use App\Enums\CostType;
use App\Enums\PaymentStatus;
use App\Models\Cost;
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
        $cost = $this->route('cost');
        $seasonId = $cost instanceof Cost ? $cost->season_id : null;

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
            'harvest_id' => ['nullable', 'uuid', Rule::exists('harvests', 'id')->where('season_id', $seasonId)],
        ];
    }
}
