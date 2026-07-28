<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Builder\Concerns;

/**
 * Semantic entry points into the automatic data detection performed by
 * DataResolver inside the pipeline (models, resources, paginators,
 * collections, DTOs, arrays are all handled transparently by ->data()).
 * These exist purely for readability at the call site.
 */
trait HasDataDetection
{
    public function from(mixed $data, ?string $message = null): static
    {
        return $this->success()->statusCode(200)->message($message ?? $this->defaultMessage('success'))->data($data);
    }

    public function paginate(mixed $paginator, ?string $message = null): static
    {
        return $this->from($paginator, $message);
    }

    public function resource(mixed $resource, ?string $message = null): static
    {
        return $this->from($resource, $message);
    }

    public function resourceCollection(mixed $collection, ?string $message = null): static
    {
        return $this->from($collection, $message);
    }
}
