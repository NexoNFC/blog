<?php

namespace App\Support;

use Illuminate\Support\Str;

class ContentSummary
{
    public static function forDisplay(?string $summary, ?string $body): ?string
    {
        $summary = trim((string) $summary);
        $bodyPlain = self::plain((string) $body);

        if ($summary === '') {
            return null;
        }

        if (preg_match('/(\w\.\.|\.\.\.|…)$/u', $summary) === 1) {
            return null;
        }

        $normalizedSummary = rtrim($summary, ".… \t\n\r\0\x0B");

        if ($bodyPlain !== '' && str_starts_with($bodyPlain, $normalizedSummary)) {
            return null;
        }

        return $summary;
    }

    public static function make(string $body, int $max = 220): string
    {
        $bodyPlain = self::plain($body);

        if ($bodyPlain === '') {
            return '';
        }

        if (Str::length($bodyPlain) <= $max) {
            return $bodyPlain;
        }

        $slice = Str::substr($bodyPlain, 0, $max);

        if (preg_match('/^(.*?[\.!?])(?:\s|$)/u', $slice, $matches) === 1) {
            $sentence = trim($matches[1]);

            if (Str::length($sentence) >= 40) {
                return $sentence;
            }
        }

        $space = Str::length($slice) > 0 ? mb_strrpos($slice, ' ') : false;

        if ($space !== false && $space > 40) {
            return rtrim(Str::substr($slice, 0, $space), ',;:').'…';
        }

        return rtrim($slice, ',;:').'…';
    }

    private static function plain(string $value): string
    {
        $plain = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plain = preg_replace('/\s+/u', ' ', $plain) ?? '';

        return trim($plain);
    }
}
