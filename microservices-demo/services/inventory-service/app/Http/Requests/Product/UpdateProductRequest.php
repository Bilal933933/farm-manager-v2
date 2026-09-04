<?php

namespace App\Http\Requests\Product;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Traits\ExtractsRequestContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    use ExtractsRequestContext;

    public function authorize(): bool
    {
        return $this->hasPermission($this, 'update_products');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $product = $this->route('product');
        $productId = $product instanceof Product ? $product->id : $product;
        $companyId = $this->attributes->get('company_id');

        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('products', 'name')->where('company_id', $companyId)->ignore($productId)],
            'description' => ['nullable', 'string'],
            'unit' => ['sometimes', 'string', 'max:20'],
            'category' => ['nullable', 'string', 'max:100'],
            'status' => ['sometimes', Rule::enum(ProductStatus::class)],
        ];
    }
}
