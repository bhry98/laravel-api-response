<?php

namespace Bhry98\LaravelApiResponse\Facades;

use Bhry98\LaravelApiResponse\Responses\NotFoundResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method static NotFoundResponse make(array $options = [])
 */
class BResponseNotFound extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'bResponse.notFound';
    }
}
