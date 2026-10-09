<?php

namespace Tests\Feature;

use App\Support\ContentBodyFormatter;
use App\Support\FescContentCleaner;
use Tests\TestCase;

class ContentBodyFormatterTest extends TestCase
{
    public function test_clean_text_moves_contact_email_to_its_own_paragraph(): void
    {
        $cleaned = app(FescContentCleaner::class)->cleanText(
            'En la FESC, Tu Futuro Sí Es Posible. Oficina de Comunicaciones FESC PBX. (607) 578 4878 Ext. 129 / comunicaciones@fesc.edu.co',
        );

        $this->assertSame(
            "En la FESC, Tu Futuro Sí Es Posible.\n\nOficina de Comunicaciones FESC\n\nPBX. (607) 578 4878 Ext. 129\n\ncomunicaciones@fesc.edu.co",
            $cleaned,
        );
    }

    public function test_html_to_text_preserves_block_paragraphs_from_fesc_markup(): void
    {
        $html = <<<'HTML'
            <div style="text-align: justify;">La jornada resultó enriquecedora, en coherencia con su misión institucional.</div>
            <div>&nbsp;</div>
            <div style="text-align: justify;">En la FESC, Tu Futuro Sí Es Posible.</div>
            <div>&nbsp;</div>
            <div>Oficina de Comunicaciones FESC</div>
            <div>PBX. (607) 578 4878 Ext. 129 / comunicaciones@fesc.edu.co</div>
            HTML;

        $text = app(FescContentCleaner::class)->htmlToText($html);

        $this->assertStringContainsString(
            "en coherencia con su misión institucional.\n\nEn la FESC, Tu Futuro Sí Es Posible.",
            $text,
        );
        $this->assertStringContainsString("Oficina de Comunicaciones FESC\n\nPBX.", $text);
        $this->assertStringEndsWith('comunicaciones@fesc.edu.co', $text);
    }

    public function test_body_formatter_renders_mailto_links(): void
    {
        $html = app(ContentBodyFormatter::class)->toHtml(
            "Párrafo introductorio.\n\ncomunicaciones@fesc.edu.co",
        );

        $this->assertStringContainsString('<p>Párrafo introductorio.</p>', $html);
        $this->assertStringContainsString('href="mailto:comunicaciones@fesc.edu.co"', $html);
        $this->assertStringContainsString('>comunicaciones@fesc.edu.co</a>', $html);
    }
}
