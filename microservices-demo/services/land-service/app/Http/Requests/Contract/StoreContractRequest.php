<?php

namespace App\Http\Requests\Contract;

use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\PaymentTerms;
use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContractRequest extends FormRequest
{
    use ExtractsRequestContext;

    public function authorize(): bool
    {
        return $this->hasPermission($this, 'create_contracts');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'contract_type' => ['required', Rule::enum(ContractType::class)],
            'counterparty_party_id' => ['required', 'uuid'],
            'owner_party_id' => ['nullable', 'uuid'],
            'financial_value' => ['required_if:contract_type,rent_in,rent_out,management', 'nullable', 'numeric', 'min:0'],
            'revenue_share_percentage' => ['required_if:contract_type,sharecropping', 'nullable', 'numeric', 'between:0,100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'payment_terms' => ['nullable', Rule::enum(PaymentTerms::class)],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', Rule::enum(ContractStatus::class)],
        ];
    }
}
