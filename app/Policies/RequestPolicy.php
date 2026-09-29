<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Request as RequestModel;

class RequestPolicy
{
    public function view(User $user, RequestModel $request): bool
    {
        return $user->id === $request->requester_id || 
               $user->id === $request->recipient_id || 
               $user->role === 'admin';
    }

    public function update(User $user, RequestModel $request): bool
    {
        // Only recipient can update request status
        return $user->id === $request->recipient_id;
    }

    public function create(User $user): bool
    {
        return $user->role !== 'guest';
    }

    public function cancel(User $user, RequestModel $request): bool
    {
        // Requester can cancel their request
        return $user->id === $request->requester_id && 
               in_array($request->status, ['new', 'alternative_proposed', 'accepted']);
    }
}
