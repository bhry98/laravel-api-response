<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Pipeline\Stages;

use Bhry98\LaravelApiResponse\Contracts\PipelineStageContract;
use Bhry98\LaravelApiResponse\Data\ResponsePayload;
use Bhry98\LaravelApiResponse\Enums\MetaKey;
use Bhry98\LaravelApiResponse\Support\MetaProviderRegistry;
use Closure;

class ApplyGlobalMetaStage implements PipelineStageContract
{
    public function __construct(
        private readonly MetaProviderRegistry $registry,
    ) {}

    public function handle(ResponsePayload $payload, Closure $next): mixed
    {
        // Explicit/resolved meta on the payload always wins over global providers.
        $meta = [...$this->registry->collect(), ...$payload->meta];

        if ($payload->version !== null) {
            $meta[MetaKey::ApiVersion->value] = $payload->version;
        }

        $payload->meta = $meta;

        return $next($payload);
    }
}
