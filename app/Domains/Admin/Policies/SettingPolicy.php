<?php

namespace App\Domains\Admin\Policies;

use App\Domains\Auth\Models\User;

class SettingPolicy
{
    public function view(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'owner']);
    }

    public function update(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'owner']);
    }
}
