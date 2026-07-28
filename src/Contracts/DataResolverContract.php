<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Contracts;

/**
 * Normalizes arbitrary data (Models, Resources, Paginators, Collections, DTOs, ...)
 * into a flat shape the builder can distribute across data/meta/links.
 */
interface DataResolverContract
{
    /**
     * @return array{data: mixed, meta: array<string, mixed>, links: array<string, mixed>}
     */
    public function resolve(mixed $data): array;

    public function supports(mixed $data): bool;
}
