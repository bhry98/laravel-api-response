<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Presets;

use Bhry98\LaravelApiResponse\Contracts\PresetContract;
use Bhry98\LaravelApiResponse\Contracts\ResponseFormatterContract;
use Bhry98\LaravelApiResponse\Formatters\DefaultFormatter;

class DefaultPreset implements PresetContract
{
    public function __construct(
        private readonly DefaultFormatter $formatter,
    ) {}

    public function formatter(): ResponseFormatterContract
    {
        return $this->formatter;
    }
}
