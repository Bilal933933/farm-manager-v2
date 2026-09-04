<?php

namespace App\Http\Requests\Party;

use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePartyRequest extends FormRequest
{
    use ExtractsRequestContext;

    public function authorize(): bool
    {
        return $this->hasPermission($this, 'create_parties');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $companyId = $this->attributes->get('company_id');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('parties', 'name')->where('company_id', $companyId)],
            'phone' => ['required', 'string', 'max:50', Rule::unique('parties', 'phone')->where('company_id', $companyId)],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
