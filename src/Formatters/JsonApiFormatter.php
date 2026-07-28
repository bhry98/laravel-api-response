<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Formatters;

use Bhry98\LaravelApiResponse\Data\ResponsePayload;

/**
 * A JSON:API-inspired envelope: {data, meta, links, errors}. This is not a
 * strict json:api spec implementation (no `type`/`id`/resource-object
 * negotiation) - it simply moves `message` and `status` into `meta` and
 * reshapes validation errors into a JSON:API-style error list.
 */
class JsonApiFormatter extends DefaultFormatter
{
    public function format(ResponsePayload $payload): array
    {
        $meta = [...$payload->meta, 'message' => $payload->message];

        if (! empty($payload->extra)) {
            $meta = [...$meta, ...$payload->extra];
        }

        $body = [
            'data' => $payload->success ? $payload->data : null,
            'meta' => $meta,
        ];

        if (! empty($payload->links)) {
            $body['links'] = $payload->links;
        }

        if (! empty($payload->errors)) {
            $body['errors'] = $this->toJsonApiErrors($payload);
        }

        if ($payload->debug !== null) {
            $body['debug'] = $payload->debug;
        }

        return $this->wrap($body);
    }

    private function toJsonApiErrors(ResponsePayload $payload): array
    {
        $errors = [];

        foreach ($payload->errors as $field => $messages) {
            foreach ((array) $messages as $message) {
                $errors[] = [
                    'status' => (string) $payload->statusCode,
                    'title' => $payload->message,
                    'detail' => $message,
                    'source' => ['pointer' => "/{$field}"],
                ];
            }
        }

        return $errors;
    }
}
