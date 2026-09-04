<?php

namespace App\Http\Requests\Season;

use App\Enums\SeasonStatus;
use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSeasonRequest extends FormRequest
{
    use ExtractsRequestContext;

    public function authorize(): bool
    {
        return $this->hasPermission($this, 'create_seasons');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'uuid'],
            'name' => ['nullable', 'string', 'max:150'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'expected_yield' => ['nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', Rule::enum(SeasonStatus::class)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
