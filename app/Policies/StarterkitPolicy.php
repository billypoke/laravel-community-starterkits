<?php

namespace App\Policies;

use App\Models\Starterkit;
use App\Models\User;

class StarterkitPolicy
{
    public function update(User $user, Starterkit $starterkit): bool
    {
        return $starterkit->user_id === $user->id || $user->isAdmin();
    }

    public function delete(User $user, Starterkit $starterkit): bool
    {
        return $starterkit->user_id === $user->id || $user->isAdmin();
    }
}
