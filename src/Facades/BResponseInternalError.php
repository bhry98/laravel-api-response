<?php

namespace Bhry98\LaravelApiResponse\Facades;

use Bhry98\LaravelApiResponse\Responses\InternalErrorResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method static InternalErrorResponse make(array $options = [])
 *
 * @deprecated Use the ApiResponse facade / api() helper instead.
 */
class BResponseInternalError extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'bResponse.internalError';
    }
}
