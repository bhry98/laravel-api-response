<?php

declare(strict_types=1);

use Bhry98\LaravelApiResponse\Builder\ResponseBuilder;

if (! function_exists('api')) {
    /**
     * Resolve a fresh ApiResponse builder instance.
     *
     * return api()->success()->message('User created.')->data($user)->send();
     * return api()->created($user);
     */
    function api(): ResponseBuilder
    {
        return app(ResponseBuilder::class);
    }
}
