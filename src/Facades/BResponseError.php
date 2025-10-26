<?php

namespace Bhry98\LaravelApiResponse\Facades;

use Bhry98\LaravelApiResponse\Responses\ErrorResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ErrorResponse make(array $options = [])
 */
class BResponseError extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'bResponse.error';
    }
}
