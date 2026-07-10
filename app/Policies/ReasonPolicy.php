<?php

namespace App\Policies;

use App\Models\Reason;
use App\Models\User;

class ReasonPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Reason $reason): bool
    {
        return $reason->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Reason $reason): bool
    {
        return $reason->user_id === $user->id;
    }

    public function delete(User $user, Reason $reason): bool
    {
        return $reason->user_id === $user->id;
    }
}
