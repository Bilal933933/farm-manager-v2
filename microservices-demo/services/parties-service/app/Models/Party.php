<?php

namespace App\Models;

use App\Enums\PartyStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Party extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'company_id',
        'name',
        'phone',
        'email',
        'address',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => PartyStatus::class,
        ];
    }

    /**
     * @return HasMany<PartyRole, $this>
     */
    public function roles(): HasMany
    {
        return $this->hasMany(PartyRole::class);
    }
}
