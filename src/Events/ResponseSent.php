<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Events;

use Illuminate\Http\JsonResponse;

/**
 * Fired once the final JsonResponse has been built and is about to be
 * returned to the framework.
 */
final class ResponseSent
{
    public function __construct(
        public readonly JsonResponse $response,
    ) {}
}
