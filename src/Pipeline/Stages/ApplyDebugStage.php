<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Pipeline\Stages;

use Bhry98\LaravelApiResponse\Contracts\PipelineStageContract;
use Bhry98\LaravelApiResponse\Data\ResponsePayload;
use Bhry98\LaravelApiResponse\Support\DebugCollector;
use Closure;
use Illuminate\Contracts\Config\Repository;

class ApplyDebugStage implements PipelineStageContract
{
    public function __construct(
        private readonly Repository $config,
        private readonly DebugCollector $collector,
    ) {}

    public function handle(ResponsePayload $payload, Closure $next): mixed
    {
        if ($this->config->get('bhry98-api-response.debug.enabled') && $this->config->get('app.debug')) {
            // Merge rather than overwrite, so details attached explicitly via
            // ->trace($exception) survive the global debug block.
            $payload->debug = [...$this->collector->collect(), ...($payload->debug ?? [])];
        }

        return $next($payload);
    }
}
