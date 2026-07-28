<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Exceptions;

use InvalidArgumentException;

class PresetNotFoundException extends InvalidArgumentException
{
    public static function make(string $preset): self
    {
        return new self("Api response preset [{$preset}] is not registered.");
    }
}
