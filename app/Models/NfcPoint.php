<?php

namespace App\Models;

use App\Enums\NfcPointStatus;
use Database\Factories\NfcPointFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'identifier',
    'code',
    'name',
    'location',
    'description',
    'status',
    'kind',
    'image_path',
    'news_id',
])]
class NfcPoint extends Model
{
    /** @use HasFactory<NfcPointFactory> */
    use HasFactory;

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => NfcPointStatus::class,
        ];
    }

    /**
     * @return BelongsTo<News, $this>
     */
    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    /**
     * @return HasMany<NfcScan, $this>
     */
    public function scans(): HasMany
    {
        return $this->hasMany(NfcScan::class);
    }

    public function isActive(): bool
    {
        return $this->status === NfcPointStatus::Active;
    }

    /**
     * @return array<string, mixed>
     */
    public function toPublicArray(): array
    {
        return [
            'code' => $this->code,
            'identifier' => $this->identifier,
            'name' => $this->name,
            'location' => $this->location,
            'description' => $this->description,
            'status' => $this->status->value,
            'kind' => $this->kind,
            'image' => $this->image_path,
            'content_slugs' => $this->news?->slug ? [$this->news->slug] : [],
            'scans_count' => $this->scans_count ?? 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toCampusLocationArray(): array
    {
        return [
            'name' => $this->name,
            'detail' => $this->location,
            'icon' => match ($this->kind) {
                'acceso' => 'home',
                'espacio' => $this->code === 'biblioteca' ? 'address-card' : 'users-alt',
                default => 'degrees-360',
            },
            'badge' => match ($this->kind) {
                'acceso' => 'Acceso',
                'espacio' => 'Espacio',
                default => 'Bloque',
            },
            'image' => $this->image_path ?? 'images/campus/fachada.jpg',
            'href' => route('nfc.show', $this->code),
        ];
    }
}
