<?php

namespace App\Actions\Party;

use App\Models\Party;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class SearchPartiesAction
{
    /**
     * Search parties with filters, sorting, and pagination.
     *
     * @param  array<string, mixed>  $filters
     */
    public function execute(string $companyId, array $filters = []): LengthAwarePaginator
    {
        $cacheKey = "parties:search:{$companyId}:".md5(json_encode($filters));

        return Cache::remember($cacheKey, 3600, function () use ($companyId, $filters) {
            $query = Party::query()->forCompany($companyId);

            // Apply search filter
            if (! empty($filters['search'])) {
                $query->search($filters['search']);
            }

            // Apply status filter
            if (! empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            // Apply role filter
            if (! empty($filters['role'])) {
                $query->withRole($filters['role']);
            }

            // Apply sorting
            $sortBy = $filters['sort_by'] ?? 'created_at';
            $sortOrder = $filters['sort_order'] ?? 'desc';

            match ($sortBy) {
                'name' => $query->orderByName($sortOrder),
                'created_at' => $query->orderBy('created_at', $sortOrder),
                'updated_at' => $query->orderBy('updated_at', $sortOrder),
                default => $query->latest(),
            };

            // Apply pagination
            $perPage = $filters['per_page'] ?? 15;
            $perPage = min($perPage, 100); // Max 100 items per page

            return $query->paginate($perPage);
        });
    }
}
