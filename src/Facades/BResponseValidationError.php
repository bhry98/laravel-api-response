<?php

namespace Bhry98\LaravelApiResponse\Facades;

use Bhry98\LaravelApiResponse\Responses\ValidationErrorResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ValidationErrorResponse make(array $options = [])
 */
class BResponseValidationError extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'bResponse.validationError';
    }
}
