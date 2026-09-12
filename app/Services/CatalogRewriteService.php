<?php

namespace App\Services;

use App\Exceptions\AiRewriteException;
use App\Models\News;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class CatalogRewriteService
{
    public function isConfigured(): bool
    {
        return filled(config('ai.api_key'));
    }

    public function rewrite(News $news): News
    {
        $sourceText = trim((string) ($news->importedContent?->raw_text ?: $news->body));

        if ($sourceText === '') {
            throw new AiRewriteException('Esta noticia no tiene texto de origen para transcribir.');
        }

        $payload = $this->payload($news);
        $rewritten = $this->requestRewrite(
            (string) ($news->importedContent?->title ?: $news->title),
            $sourceText,
        );

        $payload['original_title'] ??= $news->title;
        $payload['original_summary'] ??= $news->summary;
        $payload['original_body'] ??= $news->body;
        $payload['ai_title'] = $rewritten['title'];
        $payload['ai_summary'] = $rewritten['summary'];
        $payload['ai_body'] = $rewritten['body'];
        $payload['presentation'] = 'ai';

        $news->fill([
            'title' => $rewritten['title'],
            'summary' => $rewritten['summary'],
            'body' => $rewritten['body'],
            'processed_payload' => $payload,
            'admin_edited_at' => now(),
        ]);
        $news->save();

        return $news;
    }

    public function presentOriginal(News $news): News
    {
        $payload = $this->payload($news);
        $title = $payload['original_title'] ?? $news->importedContent?->title ?? $news->title;
        $body = $payload['original_body'] ?? $news->importedContent?->raw_text ?? $news->body;
        $summary = $payload['original_summary'] ?? Str::limit((string) $body, 220);

        $payload['presentation'] = 'original';

        $news->fill([
            'title' => $title,
            'summary' => $summary,
            'body' => $body,
            'processed_payload' => $payload,
            'admin_edited_at' => now(),
        ]);
        $news->save();

        return $news;
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(News $news): array
    {
        return is_array($news->processed_payload) ? $news->processed_payload : [];
    }

    /**
     * @return array{title: string, summary: string, body: string}
     */
    private function requestRewrite(string $title, string $sourceText): array
    {
        if (! $this->isConfigured()) {
            throw new AiRewriteException('No hay una clave de IA configurada. Revisa AI_API_KEY en el entorno.');
        }

        $endpoint = $this->completionsEndpoint();

        try {
            $response = Http::timeout((int) config('ai.timeout', 45))
                ->withToken((string) config('ai.api_key'))
                ->acceptJson()
                ->post($endpoint, [
                    'model' => (string) config('ai.model', 'deepseek-chat'),
                    'temperature' => 0.3,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Eres redactor de la plataforma informativa de la Fundación de Estudios Superiores Comfanorte (FESC). Reescribes extractos oficiales para la comunidad del campus. Conserva hechos, nombres, fechas y cifras. No inventes. Responde solo JSON.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $this->userPrompt($title, $sourceText),
                        ],
                    ],
                ]);
        } catch (ConnectionException $exception) {
            throw new AiRewriteException('No se pudo contactar el servicio de IA. Inténtalo de nuevo más tarde.', previous: $exception);
        } catch (Throwable $exception) {
            throw new AiRewriteException('No se pudo transcribir la noticia con IA.', previous: $exception);
        }

        if (! $response->successful()) {
            throw new AiRewriteException('El servicio de IA no pudo transcribir la noticia. Inténtalo de nuevo más tarde.');
        }

        $content = data_get($response->json(), 'choices.0.message.content');

        if (! is_string($content) || trim($content) === '') {
            throw new AiRewriteException('El servicio de IA devolvió una respuesta vacía.');
        }

        return $this->parseRewrite($content, $title, $sourceText);
    }

    private function completionsEndpoint(): string
    {
        $base = rtrim((string) config('ai.base_url', 'https://api.deepseek.com'), '/');

        return $base.'/chat/completions';
    }

    private function userPrompt(string $title, string $sourceText): string
    {
        return <<<PROMPT
Reescribe este extracto institucional para publicarlo en la plataforma del campus.

Título original: {$title}

Texto extraído:
{$sourceText}

Devuelve un JSON con exactamente estas claves:
- "title": título claro, máximo 120 caracteres
- "summary": resumen de 1 o 2 frases, máximo 220 caracteres
- "body": cuerpo en español, párrafos separados por una línea en blanco, sin HTML
PROMPT;
    }

    /**
     * @return array{title: string, summary: string, body: string}
     */
    private function parseRewrite(string $content, string $fallbackTitle, string $fallbackBody): array
    {
        $json = trim($content);
        $json = preg_replace('/^```(?:json)?\s*/i', '', $json) ?? $json;
        $json = preg_replace('/\s*```$/', '', $json) ?? $json;

        $decoded = json_decode($json, true);

        if (! is_array($decoded)) {
            throw new AiRewriteException('El servicio de IA devolvió un formato no válido.');
        }

        $title = $this->normalizeText((string) ($decoded['title'] ?? $fallbackTitle));
        $body = trim((string) ($decoded['body'] ?? $fallbackBody));
        $summary = $this->normalizeText((string) ($decoded['summary'] ?? Str::limit($body, 220)));

        if ($title === '' || $body === '') {
            throw new AiRewriteException('La transcripción de IA llegó incompleta.');
        }

        return [
            'title' => Str::limit($title, 255, ''),
            'summary' => Str::limit($summary, 1000, ''),
            'body' => $body,
        ];
    }

    private function normalizeText(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
    }
}
