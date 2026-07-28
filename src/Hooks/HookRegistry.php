<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Hooks;

use Bhry98\LaravelApiResponse\Data\ResponsePayload;
use Closure;

/**
 * Holds process-wide before/after hooks registered via
 * ApiResponse::before()/ApiResponse::after(). Bound as a singleton so hooks
 * registered in a service provider apply to every response built afterwards.
 */
class HookRegistry
{
    /** @var array<int, Closure(ResponsePayload): void> */
    private array $before = [];

    /** @var array<int, Closure(ResponsePayload): void> */
    private array $after = [];

    public function before(Closure $callback): void
    {
        $this->before[] = $callback;
    }

    public function after(Closure $callback): void
    {
        $this->after[] = $callback;
    }

    public function runBefore(ResponsePayload $payload): void
    {
        foreach ($this->before as $callback) {
            $callback($payload);
        }
    }

    public function runAfter(ResponsePayload $payload): void
    {
        foreach ($this->after as $callback) {
            $callback($payload);
        }
    }

    public function flush(): void
    {
        $this->before = [];
        $this->after = [];
    }
}
