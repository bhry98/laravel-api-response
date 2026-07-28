<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Exceptions;

use InvalidArgumentException;

class TemplateNotFoundException extends InvalidArgumentException
{
    public static function make(string $template): self
    {
        return new self("Api response template [{$template}] is not defined in config('bhry98-api-response.responses').");
    }
}
