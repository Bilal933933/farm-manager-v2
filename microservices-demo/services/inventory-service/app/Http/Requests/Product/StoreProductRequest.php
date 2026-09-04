<?php

namespace App\Http\Requests\Product;

use App\Enums\ProductStatus;
use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    use ExtractsRequestContext;

    public function authorize(): bool
    {
        return $this->hasPermission($this, 'create_products');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $companyId = $this->attributes->get('company_id');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('products', 'name')->where('company_id', $companyId)],
            'description' => ['nullable', 'string'],
            'unit' => ['required', 'string', 'max:20'],
            'category' => ['nullable', 'string', 'max:100'],
            'status' => ['sometimes', Rule::enum(ProductStatus::class)],
        ];
    }
}
