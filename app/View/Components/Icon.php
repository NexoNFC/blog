<?php

namespace App\View\Components;

use App\Support\Icons;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use InvalidArgumentException;

class Icon extends Component
{
    /**
     * @var array{viewBox: string, fill: string, inner: string}
     */
    public array $icon;

    public function __construct(
        public string $name,
        public bool $outline = false,
        public string $variant = 'solid',
    ) {
        $icon = Icons::resolve($name, $outline || $variant === 'outline');

        if ($icon === null) {
            throw new InvalidArgumentException("Undefined icon [{$name}].");
        }

        $this->icon = $icon;
    }

    public function render(): View
    {
        return view('components.icon');
    }
}
