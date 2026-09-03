<?php

namespace App\Models;

use App\Enums\SeasonStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Season extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'land_id',
        'product_id',
        'name',
        'start_date',
        'end_date',
        'expected_yield',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'expected_yield' => 'decimal:2',
            'status' => SeasonStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Land, $this>
     */
    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class);
    }

    /**
     * @return HasMany<Cost, $this>
     */
    public function costs(): HasMany
    {
        return $this->hasMany(Cost::class);
    }

    /**
     * @return HasMany<Harvest, $this>
     */
    public function harvests(): HasMany
    {
        return $this->hasMany(Harvest::class);
    }

    /**
     * @return HasMany<Sale, $this>
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
