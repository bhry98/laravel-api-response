<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Pipeline\Stages;

use Bhry98\LaravelApiResponse\Contracts\DataResolverContract;
use Bhry98\LaravelApiResponse\Contracts\PipelineStageContract;
use Bhry98\LaravelApiResponse\Data\ResponsePayload;
use Closure;

class ResolveDataStage implements PipelineStageContract
{
    public function __construct(
        private readonly DataResolverContract $resolver,
    ) {}

    public function handle(ResponsePayload $payload, Closure $next): mixed
    {
        if ($payload->data !== null) {
            $resolved = $this->resolver->resolve($payload->data);

            $payload->data = $resolved['data'];
            $payload->withMeta($resolved['meta'])->withLinks($resolved['links']);
        }

        return $next($payload);
    }
}
