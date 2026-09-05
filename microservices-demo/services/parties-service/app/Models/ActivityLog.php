<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Activity Log Model
 *
 * Tracks all changes made to parties and party roles.
 *
 * @property string $id
 * @property string $user_id
 * @property string $company_id
 * @property string $subject_type
 * @property string $subject_id
 * @property string $event
 * @property array $changes
 * @property string|null $description
 * @property Carbon $created_at
 */
class ActivityLog extends Model
{
    use HasUuids;

    protected $table = 'activity_logs';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'company_id',
        'subject_type',
        'subject_id',
        'event',
        'changes',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'json',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the subject of the activity.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to filter by company.
     */
    public function scopeForCompany($query, string $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeByUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by event type.
     */
    public function scopeByEvent($query, string $event)
    {
        return $query->where('event', $event);
    }

    /**
     * Scope to filter by subject type.
     */
    public function scopeBySubject($query, string $subjectType)
    {
        return $query->where('subject_type', $subjectType);
    }

    /**
     * Scope to get recent activities.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days))->orderBy('created_at', 'desc');
    }
}
