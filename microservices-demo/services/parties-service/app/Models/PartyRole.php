<?php

namespace App\Models;

use App\Enums\PartyRoleType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Party Role Model
 *
 * Represents a role assigned to a party (e.g., supplier, farmer, buyer).
 * A party can have multiple roles, but each role type can only be assigned once per party.
 *
 * @property string $id
 * @property string $party_id
 * @property PartyRoleType $role
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Party $party
 */
class PartyRole extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * The primary key type.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'party_id',
        'role',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => PartyRoleType::class,
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the party that owns the role.
     *
     * @return BelongsTo<Party, $this>
     */
    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    /**
     * Scope a query to only include roles of a specific type.
     */
    public function scopeOfType($query, PartyRoleType $type)
    {
        return $query->where('role', $type);
    }

    /**
     * Scope a query to only include supplier roles.
     */
    public function scopeSuppliers($query)
    {
        return $query->where('role', PartyRoleType::Supplier);
    }

    /**
     * Scope a query to only include farmer roles.
     */
    public function scopeFarmers($query)
    {
        return $query->where('role', PartyRoleType::Farmer);
    }

    /**
     * Scope a query to only include owner roles.
     */
    public function scopeOwners($query)
    {
        return $query->where('role', PartyRoleType::Owner);
    }

    /**
     * Scope a query to only include tenant roles.
     */
    public function scopeTenants($query)
    {
        return $query->where('role', PartyRoleType::Tenant);
    }

    /**
     * Scope a query to only include buyer roles.
     */
    public function scopeBuyers($query)
    {
        return $query->where('role', PartyRoleType::Buyer);
    }

    /**
     * Check if the role is of a specific type.
     */
    public function isType(PartyRoleType $type): bool
    {
        return $this->role === $type;
    }

    /**
     * Check if the role is a supplier.
     */
    public function isSupplier(): bool
    {
        return $this->role === PartyRoleType::Supplier;
    }

    /**
     * Check if the role is a farmer.
     */
    public function isFarmer(): bool
    {
        return $this->role === PartyRoleType::Farmer;
    }

    /**
     * Check if the role is an owner.
     */
    public function isOwner(): bool
    {
        return $this->role === PartyRoleType::Owner;
    }

    /**
     * Check if the role is a buyer.
     */
    public function isBuyer(): bool
    {
        return $this->role === PartyRoleType::Buyer;
    }
}
