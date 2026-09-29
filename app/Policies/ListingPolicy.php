<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Listing;

class ListingPolicy
{
    public function view(User $user, Listing $listing): bool
    {
        // Public listings can be viewed by anyone
        if ($listing->moderation_status === 'approved' && $listing->availability_status === 'available') {
            return true;
        }

        // Owner can view their own listings
        return $user->id === $listing->owner_user_id || $user->role === 'admin';
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
        return in_array($user->role, ['user', 'owner', 'developer']);
    }

    public function archive(User $user, Listing $listing): bool
    {
        return $user->id === $listing->owner_user_id;
    }
}
