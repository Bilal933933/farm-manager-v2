<?php

namespace App\Actions\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CreateProductAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data, string $companyId): Product
    {
        return DB::transaction(function () use ($data, $companyId) {
            $product = Product::create([...$data, 'company_id' => $companyId]);

            return $product->refresh();
        });
    }
}
