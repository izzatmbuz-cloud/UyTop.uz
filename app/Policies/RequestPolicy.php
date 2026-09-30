<?php

namespace App\Policies;

use App\Enums\RequestStatus;
use App\Enums\UserRole;
use App\Models\Request as RequestModel;
use App\Models\User;

class RequestPolicy
{
    public function view(User $user, RequestModel $request): bool
    {
        return $user->id === $request->requester_id ||
               $user->id === $request->recipient_id ||
               $user->role === UserRole::ADMIN;
    }

    public function update(User $user, RequestModel $request): bool
    {
        return $user->id === $request->recipient_id || $user->id === $request->requester_id;
    }

    public function create(User $user): bool
    {
        return $user->role !== UserRole::GUEST;
    }

    public function cancel(User $user, RequestModel $request): bool
    {
        // Requester can cancel their request
        return $user->id === $request->requester_id &&
               in_array($request->status, [RequestStatus::NEW, RequestStatus::ALTERNATIVE_PROPOSED, RequestStatus::ACCEPTED], true);
    }
}
