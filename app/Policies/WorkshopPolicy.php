<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workshop;

class WorkshopPolicy
{
    public function enroll(User $user, Workshop $workshop): bool
    {
        return $user->isEmployee() && $workshop->starts_at->isFuture();
    }
}
