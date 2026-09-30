<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Listing;
use App\Models\User;

class ListingPolicy
{
    public function view(User $user, Listing $listing): bool
    {
        // Public listings can be viewed by anyone
        if ($listing->isPubliclyVisible()) {
            return true;
        }

        // Owner can view their own listings
        return $user->id === $listing->owner_user_id || $user->role === UserRole::ADMIN;
    }

    public function update(User $user, Listing $listing): bool
    {
        return $user->id === $listing->owner_user_id;
    }

    public function delete(User $user, Listing $listing): bool
    {
        return $user->id === $listing->owner_user_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::USER, UserRole::OWNER, UserRole::DEVELOPER], true);
    }

    public function archive(User $user, Listing $listing): bool
    {
        return $user->id === $listing->owner_user_id;
    }
}
