<?php

namespace App\Http\Requests\Sale;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Season;
use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    use ExtractsRequestContext;

    public function authorize(): bool
    {
        return $this->hasPermission($this, 'create_sales');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $season = $this->route('season');
        $seasonId = $season instanceof Season ? $season->id : $season;

        return [
            'harvest_id' => ['nullable', 'uuid', Rule::exists('harvests', 'id')->where('season_id', $seasonId)],
            'product_id' => ['nullable', 'uuid'],
            'buyer_party_id' => ['required', 'uuid'],
            'buyer_name' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit' => ['required', 'string', 'max:20'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'delivery_cost' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'payment_method' => ['nullable', Rule::enum(PaymentMethod::class)],
            'date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:date'],
            'payment_status' => ['sometimes', Rule::enum(PaymentStatus::class)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
