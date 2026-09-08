<?php

namespace App\Models;

use Database\Factories\SourceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['key', 'name', 'base_url', 'is_active'])]
class Source extends Model
{
    /** @use HasFactory<SourceFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<ImportedContent, $this>
     */
    public function importedContents(): HasMany
    {
        return $this->hasMany(ImportedContent::class);
    }

    /**
     * @return HasMany<News, $this>
     */
    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }
}
