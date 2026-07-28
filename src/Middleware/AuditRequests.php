<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Middleware;

use Bhry98\LaravelApiResponse\Jobs\StoreAuditLogEntry;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Optional middleware: records every request/response it sees into the
 * "audit_log" table via the queued StoreAuditLogEntry job (see
 * config('bhry98-api-response.audit_log')), so logging never adds latency
 * to the actual response. Not registered automatically - add the
 * "api.audit" alias to a route/group.
 */
class AuditRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $config = config('bhry98-api-response.audit_log', []);

        if (! ($config['enabled'] ?? false) || ! $this->shouldLog($request, $config)) {
            return $next($request);
        }

        $start = microtime(true);

        /** @var Response $response */
        $response = $next($request);

        $this->record($request, $response, (int) round((microtime(true) - $start) * 1000), $config);

        return $response;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function shouldLog(Request $request, array $config): bool
    {
        $methods = $config['methods'] ?? [];

        if ($methods !== [] && ! in_array($request->method(), $methods, true)) {
            return false;
        }

        $routeName = $request->route()?->getName();

        foreach ($config['exclude_routes'] ?? [] as $pattern) {
            if (($routeName && fnmatch($pattern, $routeName)) || fnmatch($pattern, $request->path())) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function record(Request $request, Response $response, int $durationMs, array $config): void
    {
        $capture = $config['capture'] ?? [];
        $hidden = $config['hidden'] ?? [];
        $user = $request->user();

        $entry = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route_name' => $request->route()?->getName(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $user?->getAuthIdentifier(),
            'user_type' => $user ? $user::class : null,
            'request_headers' => ($capture['headers'] ?? false) ? $this->redact($request->headers->all(), $hidden) : null,
            'request_body' => ($capture['request_body'] ?? true) ? $this->redact($request->except([]), $hidden) : null,
            'response_status' => $response->getStatusCode(),
            'response_body' => ($capture['response_body'] ?? true) ? $this->redact($this->decodeResponse($response), $hidden) : null,
            'duration_ms' => $durationMs,
            'locale' => app()->getLocale(),
            'created_at' => now(),
        ];

        $this->dispatch(new StoreAuditLogEntry($entry), $config['queue'] ?? []);
    }

    /**
     * @param  array<string, mixed>  $queueConfig
     */
    private function dispatch(StoreAuditLogEntry $job, array $queueConfig): void
    {
        if (! ($queueConfig['enabled'] ?? true)) {
            dispatch_sync($job);

            return;
        }

        if (! empty($queueConfig['connection'])) {
            $job->onConnection($queueConfig['connection']);
        }

        if (! empty($queueConfig['queue'])) {
            $job->onQueue($queueConfig['queue']);
        }

        dispatch($job);
    }

    /**
     * @param  array<int, string>  $hidden
     */
    private function redact(mixed $data, array $hidden): mixed
    {
        if (! is_array($data) || $hidden === []) {
            return $data;
        }

        $needles = array_map('strtolower', $hidden);

        array_walk_recursive($data, function (&$value, $key) use ($needles): void {
            if (in_array(strtolower((string) $key), $needles, true)) {
                $value = '***';
            }
        });

        return $data;
    }

    private function decodeResponse(Response $response): mixed
    {
        if (! $response instanceof JsonResponse) {
            return null;
        }

        return json_decode($response->getContent() ?: 'null', true);
    }
}
