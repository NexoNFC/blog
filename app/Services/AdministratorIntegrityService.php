<?php

namespace App\Services;

use App\Exceptions\LastActiveAdministratorException;
use App\Models\User;

class AdministratorIntegrityService
{
    public function activeAdministratorCount(): int
    {
        return User::query()
            ->role('admin')
            ->where('is_active', true)
            ->count();
    }

    public function isLastActiveAdministrator(User $user): bool
    {
        if (! $user->hasRole('admin') || ! $user->is_active) {
            return false;
        }

        return $this->activeAdministratorCount() <= 1;
    }

    public function ensureCanDelete(User $user): void
    {
        if ($this->isLastActiveAdministrator($user)) {
            throw LastActiveAdministratorException::cannotDelete();
        }
    }

    public function ensureCanChangeActiveState(User $user, bool $isActive): void
    {
        if ($isActive) {
            return;
        }

        if ($this->isLastActiveAdministrator($user)) {
            throw LastActiveAdministratorException::cannotDeactivate();
        }
    }

    public function ensureCanAssignRole(User $user, string $role): void
    {
        if ($role === 'admin') {
            return;
        }

        if ($this->isLastActiveAdministrator($user)) {
            throw LastActiveAdministratorException::cannotChangeRole();
        }
    }
}
