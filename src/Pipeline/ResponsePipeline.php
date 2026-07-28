<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Pipeline;

use Bhry98\LaravelApiResponse\Data\ResponsePayload;
use Bhry98\LaravelApiResponse\Pipeline\Stages\ApplyDebugStage;
use Bhry98\LaravelApiResponse\Pipeline\Stages\ApplyGlobalMetaStage;
use Bhry98\LaravelApiResponse\Pipeline\Stages\DispatchEventsStage;
use Bhry98\LaravelApiResponse\Pipeline\Stages\ResolveDataStage;
use Bhry98\LaravelApiResponse\Pipeline\Stages\RunAfterHooksStage;
use Bhry98\LaravelApiResponse\Pipeline\Stages\RunBeforeHooksStage;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Pipeline\Pipeline as IlluminatePipeline;

/**
 * Runs a ResponsePayload through the assembly stages (data resolution,
 * global meta, debug info, hooks, events) using Laravel's own Pipeline.
 */
class ResponsePipeline
{
    /** @var array<int, class-string> */
    private array $stages = [
        ResolveDataStage::class,
        ApplyGlobalMetaStage::class,
        ApplyDebugStage::class,
        RunBeforeHooksStage::class,
        DispatchEventsStage::class,
        RunAfterHooksStage::class,
    ];

    public function __construct(
        private readonly Application $app,
    ) {}

    public function through(ResponsePayload $payload): ResponsePayload
    {
        return (new IlluminatePipeline($this->app))
            ->send($payload)
            ->through($this->stages)
            ->then(static fn (ResponsePayload $payload) => $payload);
    }
}
