<?php

namespace App\Policies;

use App\Models\User;

class NewsPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('news.view');
    }

    public function create(User $user): bool
    {
        return $user->can('news.create');
    }

    public function update(User $user): bool
    {
        return $user->can('news.update');
    }

    public function delete(User $user): bool
    {
        return $user->can('news.delete');
    }

    public function publish(User $user): bool
    {
        return $user->can('news.publish');
    }
}
