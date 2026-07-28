<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Pipeline\Stages;

use Bhry98\LaravelApiResponse\Contracts\PipelineStageContract;
use Bhry98\LaravelApiResponse\Data\ResponsePayload;
use Bhry98\LaravelApiResponse\Hooks\HookRegistry;
use Closure;

class RunBeforeHooksStage implements PipelineStageContract
{
    public function __construct(
        private readonly HookRegistry $hooks,
    ) {}

    public function handle(ResponsePayload $payload, Closure $next): mixed
    {
        $this->hooks->runBefore($payload);

        return $next($payload);
    }
}
