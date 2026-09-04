<?php

namespace App\Http\Requests\Season;

use App\Enums\SeasonStatus;
use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSeasonRequest extends FormRequest
{
    use ExtractsRequestContext;

    public function authorize(): bool
    {
        return $this->hasPermission($this, 'update_seasons');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['sometimes', 'uuid'],
            'name' => ['nullable', 'string', 'max:150'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'expected_yield' => ['nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', Rule::enum(SeasonStatus::class)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
