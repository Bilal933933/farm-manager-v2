<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    /**
     * Log an activity.
     */
    public static function log(
        string $userId,
        string $companyId,
        Model $subject,
        string $event,
        ?array $changes = null,
        ?string $description = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId,
            'company_id' => $companyId,
            'subject_type' => $subject::class,
            'subject_id' => $subject->id,
            'event' => $event,
            'changes' => $changes,
            'description' => $description,
        ]);
    }

    /**
     * Log party created.
     */
    public static function partyCreated(string $userId, string $companyId, Model $party): ActivityLog
    {
        return self::log($userId, $companyId, $party, 'created', null, 'Party created');
    }

    /**
     * Log party updated.
     */
    public static function partyUpdated(string $userId, string $companyId, Model $party, array $changes): ActivityLog
    {
        return self::log($userId, $companyId, $party, 'updated', $changes, 'Party updated');
    }

    /**
     * Log party deleted.
     */
    public static function partyDeleted(string $userId, string $companyId, Model $party): ActivityLog
    {
        return self::log($userId, $companyId, $party, 'deleted', null, 'Party deleted');
    }

    /**
     * Log role created.
     */
    public static function roleCreated(string $userId, string $companyId, Model $role): ActivityLog
    {
        return self::log($userId, $companyId, $role, 'created', null, 'Role added to party');
    }

    /**
     * Log role deleted.
     */
    public static function roleDeleted(string $userId, string $companyId, Model $role): ActivityLog
    {
        return self::log($userId, $companyId, $role, 'deleted', null, 'Role removed from party');
    }
}
