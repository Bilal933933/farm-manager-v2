<?php

namespace App\Models;

use App\Enums\PartyStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Party Model
 *
 * Represents a party (customer, supplier, farmer, etc.) in the system.
 * Parties belong to a company and can have multiple roles.
 *
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property string $phone
 * @property string|null $email
 * @property string|null $address
 * @property string|null $notes
 * @property PartyStatus $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, PartyRole> $roles
 * @property-read int|null $roles_count
 */
class Party extends Model
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
        'company_id',
        'name',
        'phone',
        'email',
        'address',
        'notes',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PartyStatus::class,
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the roles for the party.
     *
     * @return HasMany<PartyRole, $this>
     */
    public function roles(): HasMany
    {
        return $this->hasMany(PartyRole::class);
    }

    /**
     * Scope a query to only include active parties.
     */
    public function scopeActive($query)
    {
        return $query->where('status', PartyStatus::Active);
    }

    /**
     * Scope a query to only include inactive parties.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', PartyStatus::Inactive);
    }

    /**
     * Scope a query to only include parties for a specific company.
     */
    public function scopeForCompany($query, string $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to search parties by name, phone, or email.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to only include parties with specific role.
     */
    public function scopeWithRole($query, string $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('role', $role);
        });
    }

    /**
     * Scope a query to order parties by name.
     */
    public function scopeOrderByName($query, string $direction = 'asc')
    {
        return $query->orderBy('name', $direction);
    }

    /**
     * Scope a query to order parties by creation date.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Check if the party is active.
     */
    public function isActive(): bool
    {
        return $this->status === PartyStatus::Active;
    }

    /**
     * Check if the party is inactive.
     */
    public function isInactive(): bool
    {
        return $this->status === PartyStatus::Inactive;
    }

    /**
     * Check if the party has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('role', $role)->exists();
    }

    /**
     * Activate the party.
     */
    public function activate(): bool
    {
        $this->status = PartyStatus::Active;

        return $this->save();
    }

    /**
     * Deactivate the party.
     */
    public function deactivate(): bool
    {
        $this->status = PartyStatus::Inactive;

        return $this->save();
    }

    /**
     * Get full contact information as a formatted string.
     */
    public function getFullContactAttribute(): string
    {
        $contact = $this->name;

        if ($this->phone) {
            $contact .= " | {$this->phone}";
        }

        if ($this->email) {
            $contact .= " | {$this->email}";
        }

        return $contact;
    }
}
