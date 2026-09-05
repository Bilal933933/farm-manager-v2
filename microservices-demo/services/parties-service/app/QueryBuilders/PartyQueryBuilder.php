<?php

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

class PartyQueryBuilder extends Builder
{
    /**
     * Filter by company.
     */
    public function company(string $companyId): self
    {
        return $this->where('company_id', $companyId);
    }

    /**
     * Filter by status.
     */
    public function status(string $status): self
    {
        return $this->where('status', $status);
    }

    /**
     * Search by name, phone, or email.
     */
    public function keyword(string $keyword): self
    {
        return $this->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
                ->orWhere('phone', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%");
        });
    }

    /**
     * Filter with eager loading of roles.
     */
    public function withRoles(): self
    {
        return $this->with('roles');
    }

    /**
     * Order by name.
     */
    public function byName(string $direction = 'asc'): self
    {
        return $this->orderBy('name', $direction);
    }

    /**
     * Recent first.
     */
    public function recent(): self
    {
        return $this->orderBy('created_at', 'desc');
    }
}
