<?php

namespace Tests\Unit;

use App\Support\ContentSummary;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ContentSummaryTest extends TestCase
{
    #[Test]
    public function for_display_hides_truncated_prefix_of_body(): void
    {
        $body = 'El pasado 17 y 18 de junio se llevó a cabo la Auditoría Interna del Sistema de Gestión de la Calidad, con el propósito de verificar el cumplimiento de los requisitos establecidos en la norma ISO 9001:2015 y mejorar los procesos institucionales.';
        $summary = 'El pasado 17 y 18 de junio se llevó a cabo la Auditoría Interna del Sistema de Gestión de la Calidad, con el propósito de verificar el cumplimiento de los requisitos establecidos en la norma ISO 9001:2015 f..';

        $this->assertNull(ContentSummary::forDisplay($summary, $body));
    }

    #[Test]
    public function for_display_keeps_distinct_lead(): void
    {
        $summary = 'Resumen breve sobre la auditoría interna del sistema de calidad.';
        $body = 'El pasado 17 y 18 de junio se llevó a cabo la Auditoría Interna del Sistema de Gestión de la Calidad.';

        $this->assertSame($summary, ContentSummary::forDisplay($summary, $body));
    }

    #[Test]
    public function make_prefers_sentence_boundary(): void
    {
        $body = 'Primera frase completa sobre la auditoría de calidad. Continuación larga del texto que no debería aparecer en el resumen si hay punto previo y el límite alcanza la segunda oración.';

        $summary = ContentSummary::make($body, 120);

        $this->assertSame('Primera frase completa sobre la auditoría de calidad.', $summary);
    }
}
