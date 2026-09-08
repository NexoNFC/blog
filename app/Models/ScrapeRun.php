<?php

namespace App\Models;

use App\Enums\ScrapeRunStatus;
use Database\Factories\ScrapeRunFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'status',
    'trigger',
    'started_at',
    'finished_at',
    'contents_found',
    'contents_new',
    'contents_processed',
    'news_created',
    'error_message',
])]
class ScrapeRun extends Model
{
    /** @use HasFactory<ScrapeRunFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ScrapeRunStatus::class,
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'contents_found' => 'integer',
            'contents_new' => 'integer',
            'contents_processed' => 'integer',
            'news_created' => 'integer',
        ];
    }
}
