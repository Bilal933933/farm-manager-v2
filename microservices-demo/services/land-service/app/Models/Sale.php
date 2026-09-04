<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'season_id',
        'harvest_id',
        'product_id',
        'buyer_party_id',
        'buyer_name',
        'quantity',
        'unit',
        'unit_price',
        'total_price',
        'discount_amount',
        'tax_amount',
        'delivery_cost',
        'currency',
        'payment_method',
        'date',
        'due_date',
        'payment_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'delivery_cost' => 'decimal:2',
            'payment_method' => PaymentMethod::class,
            'date' => 'date',
            'due_date' => 'date',
            'payment_status' => PaymentStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Season, $this>
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * @return BelongsTo<Harvest, $this>
     */
    public function harvest(): BelongsTo
    {
        return $this->belongsTo(Harvest::class);
    }
}
