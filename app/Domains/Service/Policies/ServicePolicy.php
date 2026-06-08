<?php

namespace App\Domains\Service\Policies;

use App\Domains\Auth\Models\User;

class ServicePolicy
{
    public function manage(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'receptionist']);
    }

    public function delete(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
