<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Support;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Collects development-only diagnostics (SQL queries, timing, memory,
 * exception trace). Never call outside of an app.debug === true context;
 * the builder/pipeline enforce that gate, not this class.
 */
class DebugCollector
{
    public function __construct(
        private readonly Repository $config,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function collect(?Throwable $exception = null): array
    {
        $include = $this->config->get('bhry98-api-response.debug.include', []);
        $debug = [];

        if (in_array('time', $include, true)) {
            $start = defined('LARAVEL_START') ? LARAVEL_START : $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true);
            $debug['execution_time_ms'] = round((microtime(true) - $start) * 1000, 2);
        }

        if (in_array('memory', $include, true)) {
            $debug['memory_usage_mb'] = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
        }

        if (in_array('queries', $include, true)) {
            $debug['queries'] = collect(DB::getQueryLog())
                ->map(fn (array $query) => [
                    'sql' => $query['query'],
                    'bindings' => $query['bindings'],
                    'time_ms' => $query['time'],
                ])
                ->all();
        }

        if ($exception !== null) {
            if (in_array('exception', $include, true)) {
                $debug['exception'] = [
                    'class' => get_class($exception),
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile().':'.$exception->getLine(),
                ];
            }

            if (in_array('trace', $include, true)) {
                $debug['trace'] = collect($exception->getTrace())
                    ->take(20)
                    ->map(fn (array $frame) => [
                        'file' => $frame['file'] ?? null,
                        'line' => $frame['line'] ?? null,
                        'function' => $frame['function'],
                        'class' => $frame['class'] ?? null,
                    ])
                    ->all();
            }
        }

        return $debug;
    }
}
