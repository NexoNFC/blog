<?php

namespace App\Models;

use App\Enums\NfcPointStatus;
use Database\Factories\NfcPointFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'identifier',
    'code',
    'name',
    'location',
    'description',
    'status',
    'kind',
    'image_path',
    'panorama_path',
    'tour_enabled',
    'tour_description',
    'nfc_marker_theta',
    'nfc_marker_phi',
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
            'tour_enabled' => 'boolean',
            'nfc_marker_theta' => 'float',
            'nfc_marker_phi' => 'float',
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

    public function hasNfcMarker(): bool
    {
        return $this->nfc_marker_theta !== null && $this->nfc_marker_phi !== null;
    }

    public function isActive(): bool
    {
        return $this->status === NfcPointStatus::Active;
    }

    public function hasTour(): bool
    {
        return $this->tour_enabled && filled($this->panorama_path);
    }

    public function panoramaUrl(): ?string
    {
        if (! filled($this->panorama_path)) {
            return null;
        }

        if (str_starts_with($this->panorama_path, 'http://') || str_starts_with($this->panorama_path, 'https://')) {
            return $this->panorama_path;
        }

        // Ruta relativa al origen: evita textura negra por CORS si APP_URL
        // no coincide con el host con el que se abre el sitio (localhost vs 127.0.0.1).
        if (str_starts_with($this->panorama_path, 'images/')) {
            return '/'.ltrim($this->panorama_path, '/');
        }

        $storageUrl = Storage::disk('public')->url($this->panorama_path);

        if (str_starts_with($storageUrl, 'http://') || str_starts_with($storageUrl, 'https://')) {
            $path = parse_url($storageUrl, PHP_URL_PATH);

            return is_string($path) && $path !== '' ? $path : $storageUrl;
        }

        return $storageUrl;
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
            'has_tour' => $this->hasTour(),
            'tour_url' => $this->hasTour() ? route('nfc.tour', $this->code) : null,
            'tour_description' => $this->tour_description,
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
            'detail' => $this->campusCardDetail(),
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
            'href' => $this->hasTour()
                ? route('nfc.tour', $this->code)
                : route('nfc.show', $this->code),
            'has_tour' => $this->hasTour(),
        ];
    }

    private function campusCardDetail(): string
    {
        $name = trim((string) $this->name);
        $location = trim((string) $this->location);
        $description = trim((string) $this->description);

        if ($location !== '' && strcasecmp($location, $name) !== 0) {
            return $location;
        }

        return $description;
    }

    /**
     * @return array{id: string, title: string, description: string, theta: float, phi: float}|null
     */
    public function nfcMarkerArray(): ?array
    {
        if (! $this->hasNfcMarker()) {
            return null;
        }

        return [
            'id' => 'nfc-marker',
            'title' => 'Tarjeta NFC',
            'description' => 'Aquí está ubicada la tarjeta NFC de '.$this->name.'.',
            'theta' => (float) $this->nfc_marker_theta,
            'phi' => (float) $this->nfc_marker_phi,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toTourArray(): array
    {
        $marker = $this->nfcMarkerArray();

        return [
            'code' => $this->code,
            'name' => $this->name,
            'location' => $this->location,
            'description' => $this->tour_description ?: $this->description,
            'panorama_url' => $this->panoramaUrl(),
            'info_url' => route('nfc.show', $this->code),
            'marker' => $marker,
            'hotspots' => $marker ? [$marker] : [],
        ];
    }
}
