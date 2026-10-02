<?php

namespace App\Support;

class FescContentCleaner
{
    public function cleanHtml(string $html): string
    {
        $withMails = $this->revealJoomlaHiddenMails($html);
        $withoutUnsafe = preg_replace('#<(script|iframe|object|embed|form)[^>]*>.*?</\1>#is', '', $withMails) ?? $withMails;
        $withoutHandlers = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\')/i', '', $withoutUnsafe) ?? $withoutUnsafe;

        return trim($withoutHandlers);
    }

    /**
     * Convierte HTML editorial a texto preservando bloques (p, div, br, etc.).
     */
    public function htmlToText(string $html): string
    {
        $html = $this->cleanHtml($html);
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html) ?? $html;
        $html = preg_replace(
            '/<\/(p|div|h[1-6]|li|tr|blockquote|section|article|header|footer|figcaption)>/i',
            "</$1>\n\n",
            $html,
        ) ?? $html;
        $html = preg_replace('/<(?:p|div|h[1-6]|li|blockquote|section|article)(?:\s[^>]*)?>/i', "\n\n", $html) ?? $html;

        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\xc2\xa0", ' ', $text);

        return $this->cleanText($text);
    }

    public function cleanText(string $text): string
    {
        $withoutCloak = $this->stripEmailCloakPlaceholders($text);
        $withSeparatedEmail = preg_replace(
            '/\s*\/\s*([A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,})\b/iu',
            "\n\n$1",
            $withoutCloak,
        ) ?? $withoutCloak;
        $withSignatures = $this->separateSignatureBlocks($withSeparatedEmail);

        $paragraphs = preg_split("/\n\s*\n/u", $withSignatures) ?: [$withSignatures];
        $paragraphs = array_values(array_filter(array_map(
            function (string $paragraph): string {
                $line = preg_replace('/[ \t\x0B\f\r]+/u', ' ', $paragraph) ?? $paragraph;
                $line = preg_replace('/\s*\/\s*$/u', '', $line) ?? $line;

                return trim($line);
            },
            $paragraphs,
        )));

        return implode("\n\n", $paragraphs);
    }

    private function separateSignatureBlocks(string $text): string
    {
        $replacements = [
            '/(?<=[.!?…\"”»])\s+(En la FESC,\s+Tu Futuro Sí Es Posible\.?)/iu' => "\n\n$1",
            '/(?<=[.!?…\"”»])\s+(Oficina de Comunicaciones\b)/iu' => "\n\n$1",
            '/(En la FESC,\s+Tu Futuro Sí Es Posible\.?)\s+(Oficina de Comunicaciones\b)/iu' => "$1\n\n$2",
            '/(Oficina de Comunicaciones[^\n]*?)\s+(PBX\b)/iu' => "$1\n\n$2",
        ];

        foreach ($replacements as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text) ?? $text;
        }

        return $text;
    }

    private function revealJoomlaHiddenMails(string $html): string
    {
        $replaced = preg_replace_callback(
            '/<joomla-hidden-mail\b([^>]*)>.*?<\/joomla-hidden-mail>/is',
            function (array $matches): string {
                $email = $this->emailFromHiddenMailAttributes($matches[1]);

                return $email ?? '';
            },
            $html,
        );

        return $replaced ?? $html;
    }

    private function emailFromHiddenMailAttributes(string $attributes): ?string
    {
        if (preg_match('/\btext=(["\'])([^"\']*)\1/i', $attributes, $textMatch) === 1) {
            $decoded = base64_decode($textMatch[2], true);

            if (is_string($decoded) && $decoded !== '' && filter_var($decoded, FILTER_VALIDATE_EMAIL)) {
                return $decoded;
            }
        }

        if (
            preg_match('/\bfirst=(["\'])([^"\']*)\1/i', $attributes, $firstMatch) === 1
            && preg_match('/\blast=(["\'])([^"\']*)\1/i', $attributes, $lastMatch) === 1
        ) {
            $local = base64_decode($firstMatch[2], true);
            $domain = base64_decode($lastMatch[2], true);

            if (is_string($local) && is_string($domain) && $local !== '' && $domain !== '') {
                $candidate = $local.'@'.$domain;

                if (filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
                    return $candidate;
                }
            }
        }

        return null;
    }

    private function stripEmailCloakPlaceholders(string $text): string
    {
        $patterns = [
            '/Esta dirección de correo electrónico está siendo protegida contra los robots de spam\.\s*Necesita tener JavaScript habilitado para poder verla?\./iu',
            '/This email address is being protected from spambots\.\s*You need JavaScript enabled to view it\./i',
        ];

        return preg_replace($patterns, '', $text) ?? $text;
    }
}
