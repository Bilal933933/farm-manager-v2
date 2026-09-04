<?php

namespace App\Actions\Product;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ListProductsAction
{
    /**
     * @return Collection<int, Product>
     */
    public function execute(string $companyId): Collection
    {
        return Product::where('company_id', $companyId)->latest()->get();
    }
}
