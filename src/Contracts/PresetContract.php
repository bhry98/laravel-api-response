<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Contracts;

/**
 * A named, reusable bundle of formatting behaviour (e.g. "minimal", "jsonapi").
 */
interface PresetContract
{
    public function formatter(): ResponseFormatterContract;
}
