<?php

namespace App\Domains\Offer\Policies;

use App\Domains\Auth\Models\User;
use App\Domains\Offer\Models\Offer;

class OfferPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'receptionist']);
    }

    public function view(User $user, Offer $offer): bool
    {
        return $user->hasAnyRole(['admin', 'receptionist']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'receptionist']);
    }

    public function update(User $user, Offer $offer): bool
    {
        return $user->hasAnyRole(['admin', 'receptionist']);
    }

    public function delete(User $user, Offer $offer): bool
    {
        return $user->hasRole('admin');
    }
}
