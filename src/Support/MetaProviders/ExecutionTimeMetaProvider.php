<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Support\MetaProviders;

use Bhry98\LaravelApiResponse\Contracts\MetaProviderContract;

class ExecutionTimeMetaProvider implements MetaProviderContract
{
    public function key(): string
    {
        return 'execution_time';
    }

    public function value(): mixed
    {
        $start = defined('LARAVEL_START') ? LARAVEL_START : $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true);

        return round((microtime(true) - $start) * 1000, 2).'ms';
    }
}
