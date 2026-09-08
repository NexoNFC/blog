<?php

namespace App\Services;

use App\Enums\ContentStatus;
use App\Models\News;
use App\Models\NfcPoint;
use InvalidArgumentException;

class NfcAssociationService
{
    public function associate(NfcPoint $point, ?News $news): NfcPoint
    {
        if ($news !== null && $news->status !== ContentStatus::Published) {
            throw new InvalidArgumentException('Solo una noticia publicada puede asociarse a un punto NFC.');
        }

        $point->news_id = $news?->id;
        $point->save();

        return $point->refresh();
    }
}
