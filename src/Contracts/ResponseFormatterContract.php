<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Contracts;

use Bhry98\LaravelApiResponse\Data\ResponsePayload;

/**
 * Shapes a ResponsePayload into the final array that will be JSON-encoded.
 *
 * Implement this to ship a fully custom envelope structure (e.g. a strict
 * JSON:API document) and register it via config('bhry98-api-response.presets').
 */
interface ResponseFormatterContract
{
    /**
     * @return array<string, mixed>
     */
    public function format(ResponsePayload $payload): array;
}
