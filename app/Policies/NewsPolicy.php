<?php

namespace App\Policies;

use App\Models\News;
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

    public function update(User $user, News $news): bool
    {
        return $user->can('news.update');
    }

    public function delete(User $user, News $news): bool
    {
        return $user->can('news.delete');
    }

    public function publish(User $user, News $news): bool
    {
        return $user->can('news.publish');
    }

    public function archive(User $user, News $news): bool
    {
        return $user->can('news.delete') || $user->can('news.update');
    }
}
