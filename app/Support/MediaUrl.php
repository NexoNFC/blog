<?php

namespace App\Support;

class MediaUrl
{
    public static function resolve(?string $path, ?string $fallback = null): ?string
    {
        $value = trim((string) $path);

        if ($value !== '') {
            if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                return $value;
            }

            return asset($value);
        }

        if ($fallback === null || $fallback === '') {
            return null;
        }

        if (str_starts_with($fallback, 'http://') || str_starts_with($fallback, 'https://')) {
            return $fallback;
        }

        return asset($fallback);
    }
}
