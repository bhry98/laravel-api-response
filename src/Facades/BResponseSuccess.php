<?php

namespace Bhry98\LaravelApiResponse\Facades;

use Bhry98\LaravelApiResponse\Responses\SuccessResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method static SuccessResponse make(array $options = [])
 */
class BResponseSuccess extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'bResponse.success';
    }
}
