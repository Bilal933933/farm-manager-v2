<?php

namespace App\Models;

use App\Enums\LotSourceType;
use App\Enums\LotStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lot extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'company_id',
        'product_id',
        'warehouse_id',
        'source_type',
        'source_id',
        'season_id',
        'quantity',
        'reserved_quantity',
        'unit',
        'cost_per_unit',
        'harvest_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'source_type' => LotSourceType::class,
            'quantity' => 'decimal:2',
            'reserved_quantity' => 'decimal:2',
            'cost_per_unit' => 'decimal:2',
            'harvest_date' => 'date',
            'status' => LotStatus::class,
        ];
    }

    public function getAvailableQuantityAttribute(): float
    {
        return (float) $this->quantity - (float) $this->reserved_quantity;
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<Warehouse, $this>
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * @return HasMany<InventoryMovement, $this>
     */
    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * @param  Builder<Lot>  $query
     * @return Builder<Lot>
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', '!=', LotStatus::SoldOut->value)
            ->whereColumn('quantity', '>', 'reserved_quantity');
    }

    /**
     * @param  Builder<Lot>  $query
     * @return Builder<Lot>
     */
    public function scopeForCompany(Builder $query, string $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }
}
