<?php

namespace App\Http\Requests\PartyRole;

use App\Enums\PartyRoleType;
use App\Models\Party;
use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePartyRoleRequest extends FormRequest
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

        return [
            'role' => ['required', Rule::enum(PartyRoleType::class), Rule::unique('party_roles', 'role')->where('party_id', $partyId)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
