<?php

namespace App\Support;

class ContentBodyFormatter
{
    public function toHtml(string $text): string
    {
        $paragraphs = preg_split("/\n\s*\n/u", trim($text)) ?: [];
        $html = [];

        foreach ($paragraphs as $paragraph) {
            $normalized = trim(preg_replace('/\s+/u', ' ', $paragraph) ?? $paragraph);

            if ($normalized === '') {
                continue;
            }

            $html[] = '<p>'.$this->linkify(e($normalized)).'</p>';
        }

        return implode("\n", $html);
    }

    private function linkify(string $escapedText): string
    {
        return preg_replace_callback(
            '/\b([A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,})\b/i',
            function (array $matches): string {
                $email = $matches[1];

                return '<a href="mailto:'.$email.'" class="font-semibold text-primary underline-offset-2 hover:underline">'.$email.'</a>';
            },
            $escapedText,
        ) ?? $escapedText;
    }
}
