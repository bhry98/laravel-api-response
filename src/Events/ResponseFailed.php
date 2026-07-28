<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Events;

use Bhry98\LaravelApiResponse\Data\ResponsePayload;

/**
 * Fired instead of / alongside ResponseCreated whenever the payload
 * represents a failure (success === false).
 */
final class ResponseFailed
{
    public function __construct(
        public readonly ResponsePayload $payload,
    ) {}
}
