<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Formatters;

use Bhry98\LaravelApiResponse\Data\ResponsePayload;

/**
 * Only {status, message, data} (plus errors when present) - no meta, links,
 * extras or debug. Good fit for lightweight internal/mobile clients.
 */
class MinimalFormatter extends DefaultFormatter
{
    public function format(ResponsePayload $payload): array
    {
        $keys = $this->keys();

        $body = [
            $keys['status'] => $payload->success,
            $keys['message'] => $payload->message,
            $keys['data'] => $payload->data,
        ];

        if (! empty($payload->errors)) {
            $body[$keys['errors']] = $payload->errors;
        }

        return $this->wrap($body);
    }
}
