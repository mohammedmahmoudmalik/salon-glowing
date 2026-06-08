<?php

namespace App\Domains\Review\Policies;

use App\Domains\Auth\Models\User;
use App\Domains\Review\Models\Review;

class ReviewPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('customer');
    }

    public function hide(User $user, Review $review): bool
    {
        return $user->hasAnyRole(['admin', 'receptionist']);
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->hasAnyRole(['admin', 'receptionist']);
    }
}
