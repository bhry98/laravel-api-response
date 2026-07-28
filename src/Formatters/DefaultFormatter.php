<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Formatters;

use Bhry98\LaravelApiResponse\Contracts\ResponseFormatterContract;
use Bhry98\LaravelApiResponse\Data\ResponsePayload;
use Illuminate\Contracts\Config\Repository;

/**
 * The stock envelope: {status, message, data, errors?, meta?, links?}.
 * Key names are fully configurable via config('bhry98-api-response.keys').
 */
class DefaultFormatter implements ResponseFormatterContract
{
    public function __construct(
        protected readonly Repository $config,
    ) {}

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

        if (! empty($payload->meta)) {
            $body[$keys['meta']] = $payload->meta;
        }

        if (! empty($payload->links)) {
            $body[$keys['links']] = $payload->links;
        }

        if (! empty($payload->extra)) {
            $body = [...$body, ...$payload->extra];
        }

        if ($payload->debug !== null) {
            $body['debug'] = $payload->debug;
        }

        return $this->wrap($body);
    }

    /**
     * @return array<string, string>
     */
    protected function keys(): array
    {
        return [
            'status' => 'status',
            'message' => 'message',
            'data' => 'data',
            'errors' => 'errors',
            'meta' => 'meta',
            'links' => 'links',
            ...$this->config->get('bhry98-api-response.keys', []),
        ];
    }

    protected function wrap(array $body): array
    {
        return $this->config->get('bhry98-api-response.wrap', false)
            ? ['data' => $body]
            : $body;
    }
}
