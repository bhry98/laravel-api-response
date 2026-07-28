<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Events;

use Bhry98\LaravelApiResponse\Data\ResponsePayload;

/**
 * Fired once the payload has been fully assembled (data resolved, meta
 * collected) but before it is formatted and sent.
 */
final class ResponseCreated
{
    public function __construct(
        public readonly ResponsePayload $payload,
    ) {}
}
