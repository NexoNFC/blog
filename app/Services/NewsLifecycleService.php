<?php

namespace App\Services;

use App\Enums\ContentStatus;
use App\Models\News;
use InvalidArgumentException;

class NewsLifecycleService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(News $news, array $attributes): News
    {
        $news->fill($attributes);
        $news->admin_edited_at = now();
        $news->save();

        return $news;
    }

    public function publish(News $news): News
    {
        if ($news->status === ContentStatus::Archived) {
            throw new InvalidArgumentException('No se puede publicar una noticia archivada sin restaurarla antes.');
        }

        $news->status = ContentStatus::Published;
        $news->published_at ??= now();
        $news->admin_edited_at = now();
        $news->save();

        return $news;
    }

    public function archive(News $news): News
    {
        $news->status = ContentStatus::Archived;
        $news->admin_edited_at = now();
        $news->save();

        return $news;
    }
}
