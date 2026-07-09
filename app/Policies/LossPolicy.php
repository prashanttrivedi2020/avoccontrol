<?php

namespace App\Policies;

use App\Models\Loss;
use App\Models\User;

class LossPolicy
{
    public function view(User $user, Loss $loss): bool
    {
        return $user->id === $loss->user_id;
    }

    public function delete(User $user, Loss $loss): bool
    {
        return $user->id === $loss->user_id;
    }
}
