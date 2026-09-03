<?php

namespace App\Models;

use App\Enums\CostType;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cost extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'season_id',
        'cost_type',
        'product_id',
        'quantity',
        'unit_price',
        'amount',
        'date',
        'description',
        'payment_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'cost_type' => CostType::class,
            'payment_status' => PaymentStatus::class,
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'amount' => 'decimal:2',
            'date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Season, $this>
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }
}
