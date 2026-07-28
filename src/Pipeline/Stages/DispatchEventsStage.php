<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Pipeline\Stages;

use Bhry98\LaravelApiResponse\Contracts\PipelineStageContract;
use Bhry98\LaravelApiResponse\Data\ResponsePayload;
use Bhry98\LaravelApiResponse\Events\ResponseCreated;
use Bhry98\LaravelApiResponse\Events\ResponseFailed;
use Closure;
use Illuminate\Contracts\Events\Dispatcher;

class DispatchEventsStage implements PipelineStageContract
{
    public function __construct(
        private readonly Dispatcher $events,
    ) {}

    public function handle(ResponsePayload $payload, Closure $next): mixed
    {
        $this->events->dispatch(new ResponseCreated($payload));

        if (! $payload->success) {
            $this->events->dispatch(new ResponseFailed($payload));
        }

        return $next($payload);
    }
}
