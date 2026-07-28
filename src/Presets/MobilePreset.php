<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Presets;

use Bhry98\LaravelApiResponse\Contracts\PresetContract;
use Bhry98\LaravelApiResponse\Contracts\ResponseFormatterContract;
use Bhry98\LaravelApiResponse\Formatters\MobileFormatter;

class MobilePreset implements PresetContract
{
    public function __construct(
        private readonly MobileFormatter $formatter,
    ) {}

    public function formatter(): ResponseFormatterContract
    {
        return $this->formatter;
    }
}
