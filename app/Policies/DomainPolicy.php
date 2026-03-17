<?php

namespace App\Policies;

use App\Models\Domain;
use App\Models\User;

class DomainPolicy
{
    public function view(User $user, Domain $domain): bool
    {
        return $user->role === 'admin' || $domain->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'client'], true);
    }

    public function update(User $user, Domain $domain): bool
    {
        return $user->role === 'admin' || $domain->user_id === $user->id;
    }

    public function delete(User $user, Domain $domain): bool
    {
        return $user->role === 'admin' || $domain->user_id === $user->id;
    }
}
