<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Middleware;

use Bhry98\LaravelApiResponse\Builder\ResponseBuilder;
use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Optional middleware: throttles requests using
 * config('bhry98-api-response.rate_limit') as the default, returning a
 * standard api()->tooManyRequests() envelope (with Retry-After/
 * X-RateLimit-* headers) instead of Laravel's ThrottleRequestsException.
 * Not registered automatically - add the "api.rate_limit" alias to a
 * route/group. Middleware parameters override the configured
 * max_attempts/decay_seconds for that route:
 * ->middleware('api.rate_limit:100,1')
 */
class ApiRateLimit
{
    public function __construct(
        private readonly RateLimiter $limiter,
    ) {}

    public function handle(Request $request, Closure $next, ?int $maxAttempts = null, ?int $decaySeconds = null): Response
    {
        $config = config('bhry98-api-response.rate_limit', []);

        if (! ($config['enabled'] ?? false)) {
            return $next($request);
        }

        $maxAttempts ??= (int) ($config['max_attempts'] ?? 60);
        $decaySeconds ??= (int) ($config['decay_seconds'] ?? 60);
        $key = $this->resolveKey($request, $config);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return $this->buildLimitResponse($key, $maxAttempts, $config);
        }

        $this->limiter->hit($key, $decaySeconds);

        /** @var Response $response */
        $response = $next($request);

        return $this->applySuccessHeaders($response, $key, $maxAttempts);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function resolveKey(Request $request, array $config): string
    {
        $prefix = $config['key_prefix'] ?? 'api-response-rate-limit';
        $by = $config['by'] ?? 'ip';

        $identifier = match ($by) {
            'user' => (string) ($request->user()?->getAuthIdentifier() ?? $request->ip()),
            'route' => $request->route()?->getName() ?? $request->path(),
            default => (string) $request->ip(),
        };

        return sha1($prefix.'|'.$by.'|'.$identifier);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function buildLimitResponse(string $key, int $maxAttempts, array $config): Response
    {
        $retryAfter = $this->limiter->availableIn($key);

        return app(ResponseBuilder::class)
            ->tooManyRequests($config['message'] ?? null)
            ->headers([
                'Retry-After' => (string) $retryAfter,
                'X-RateLimit-Limit' => (string) $maxAttempts,
                'X-RateLimit-Remaining' => '0',
            ])
            ->send();
    }

    private function applySuccessHeaders(Response $response, string $key, int $maxAttempts): Response
    {
        $remaining = max(0, $this->limiter->remaining($key, $maxAttempts));

        $response->headers->set('X-RateLimit-Limit', (string) $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', (string) $remaining);

        return $response;
    }
}
