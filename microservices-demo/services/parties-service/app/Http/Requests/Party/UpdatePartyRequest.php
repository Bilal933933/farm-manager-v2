<?php

namespace App\Http\Requests\Party;

use App\Enums\PartyStatus;
use App\Models\Party;
use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePartyRequest extends FormRequest
{
    use ExtractsRequestContext;

    public function authorize(): bool
    {
        return $this->hasPermission($this, 'update_parties');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $party = $this->route('party');
        $partyId = $party instanceof Party ? $party->id : $party;
        $companyId = $this->attributes->get('company_id');

        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('parties', 'name')->where('company_id', $companyId)->ignore($partyId)],
            'phone' => ['sometimes', 'string', 'max:50', Rule::unique('parties', 'phone')->where('company_id', $companyId)->ignore($partyId)],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', Rule::enum(PartyStatus::class)],
        ];
    }
}
