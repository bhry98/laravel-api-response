<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Presets;

use Bhry98\LaravelApiResponse\Contracts\PresetContract;
use Bhry98\LaravelApiResponse\Contracts\ResponseFormatterContract;
use Bhry98\LaravelApiResponse\Formatters\MinimalFormatter;

class MinimalPreset implements PresetContract
{
    public function __construct(
        private readonly MinimalFormatter $formatter,
    ) {}

    public function formatter(): ResponseFormatterContract
    {
        return $this->formatter;
    }
}
