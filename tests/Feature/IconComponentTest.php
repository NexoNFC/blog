<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\ViewException;
use InvalidArgumentException;
use Tests\TestCase;

class IconComponentTest extends TestCase
{
    public function test_icon_renders_by_name(): void
    {
        $html = Blade::render('<x-icon name="bell" />');

        $this->assertStringContainsString('<svg', $html);
        $this->assertStringContainsString('viewBox="0 0 14 20"', $html);
        $this->assertStringContainsString('aria-hidden="true"', $html);
    }

    public function test_icon_renders_outline_variant(): void
    {
        $html = Blade::render('<x-icon name="bell" outline />');

        $this->assertStringContainsString('viewBox="0 0 16 21"', $html);
        $this->assertStringContainsString('stroke="currentColor"', $html);
    }

    public function test_icon_accepts_custom_classes(): void
    {
        $html = Blade::render('<x-icon name="arrow-right" class="h-4 w-4 text-primary" />');

        $this->assertStringContainsString('h-4 w-4 text-primary', $html);
    }

    public function test_unknown_icon_throws(): void
    {
        try {
            Blade::render('<x-icon name="does-not-exist" />');
            $this->fail('Expected an exception for an undefined icon.');
        } catch (ViewException $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception->getPrevious());
            $this->assertSame('Undefined icon [does-not-exist].', $exception->getPrevious()?->getMessage());
        }
    }
}
