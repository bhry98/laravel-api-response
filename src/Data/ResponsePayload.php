<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Data;

use Symfony\Component\HttpFoundation\Cookie;

/**
 * Mutable value object carried through the response pipeline.
 *
 * This is intentionally mutable (rather than a readonly DTO) because pipeline
 * stages progressively enrich it (resolved data, global meta, debug info, ...)
 * before the final formatter turns it into a plain array.
 */
final class ResponsePayload
{
    /**
     * @param  array<string, mixed>  $meta
     * @param  array<string, mixed>  $links
     * @param  array<string, mixed>  $errors
     * @param  array<string, mixed>  $extra
     * @param  array<string, mixed>|null  $debug
     * @param  array<string, string>  $headers
     * @param  array<int, Cookie>  $cookies
     */
    public function __construct(
        public bool $success = true,
        public string $message = '',
        public mixed $data = null,
        public array $meta = [],
        public array $links = [],
        public array $errors = [],
        public array $extra = [],
        public ?array $debug = null,
        public int $statusCode = 200,
        public array $headers = [],
        public array $cookies = [],
        public ?string $preset = null,
        public ?string $version = null,
    ) {}

    public function withMeta(array $meta): static
    {
        $this->meta = [...$this->meta, ...$meta];

        return $this;
    }

    public function withLinks(array $links): static
    {
        $this->links = [...$this->links, ...$links];

        return $this;
    }
}
