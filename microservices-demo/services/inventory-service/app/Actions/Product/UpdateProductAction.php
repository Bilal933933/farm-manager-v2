<?php

namespace App\Actions\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class UpdateProductAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update($data);

            return $product->refresh();
        });
    }
}
