<?php

namespace App\Models;

use App\Enums\AreaUnit;
use App\Enums\LandStatus;
use App\Enums\OwnershipType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Land extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'company_id',
        'slug',
        'name',
        'description',
        'area',
        'area_unit',
        'map_coordinates',
        'ownership_type',
        'owner_party_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'area' => 'decimal:2',
            'area_unit' => AreaUnit::class,
            'ownership_type' => OwnershipType::class,
            'status' => LandStatus::class,
            'map_coordinates' => 'array',
        ];
    }

    /**
     * @return HasMany<Season, $this>
     */
    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    /**
     * @return HasMany<Contract, $this>
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}
