<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Contracts;

/**
 * A single piece of global metadata (request id, execution time, ...) that can
 * be automatically attached to every response. Register custom providers in
 * config('bhry98-api-response.meta.providers').
 */
interface MetaProviderContract
{
    public function key(): string;

    public function value(): mixed;
}
