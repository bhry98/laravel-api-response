<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Contracts;

use Bhry98\LaravelApiResponse\Data\ResponsePayload;
use Closure;

/**
 * A single step in the response assembly pipeline. Compatible with
 * Illuminate\Pipeline\Pipeline (uses the default `handle` method).
 */
interface PipelineStageContract
{
    public function handle(ResponsePayload $payload, Closure $next): mixed;
}
