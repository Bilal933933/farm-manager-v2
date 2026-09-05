<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ActivityLogResource;
use App\Models\ActivityLog;
use App\Traits\ExtractsRequestContext;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    use ExtractsRequestContext;

    /**
     * Get activity logs for the company.
     */
    public function index(Request $request)
    {
        $companyId = $this->getCompanyId($request);

        $logs = ActivityLog::forCompany((string) $companyId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return ActivityLogResource::collection($logs);
    }

    /**
     * Get activity logs for a specific party.
     */
    public function party(Request $request, string $partyId)
    {
        $companyId = $this->getCompanyId($request);

        $logs = ActivityLog::forCompany((string) $companyId)
            ->where('subject_id', $partyId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return ActivityLogResource::collection($logs);
    }
}
