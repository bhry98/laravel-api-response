<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Optional middleware: resolves the request's locale and calls
 * App::setLocale(). Priority order:
 *   1. The "Accept-Language" header (first tag, matched against
 *      config('...locale.supported') when that list is non-empty).
 *   2. The authenticated user's locale column - the column name is
 *      configurable via config('...locale.user_column') so this works
 *      whatever your users table calls it ("locale", "lang", ...).
 *   3. config('...locale.fallback') (defaults to config('app.locale')).
 *
 * Not registered automatically - add the "api.locale" alias to a
 * route/group.
 */
class ResolveLocale
{
    public function __construct(
        private readonly Application $app,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $config = config('bhry98-api-response.locale', []);

        if ($config['enabled'] ?? false) {
            $this->app->setLocale($this->resolve($request, $config));
        }

        return $next($request);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function resolve(Request $request, array $config): string
    {
        $supported = $config['supported'] ?? [];

        $fromHeader = $this->fromHeader($request, $supported);

        if ($fromHeader !== null) {
            return $fromHeader;
        }

        $fromUser = $this->fromUser($request, $config['user_column'] ?? 'locale', $supported);

        if ($fromUser !== null) {
            return $fromUser;
        }

        return $config['fallback'] ?? $this->app->getLocale();
    }

    /**
     * @param  array<int, string>  $supported
     */
    private function fromHeader(Request $request, array $supported): ?string
    {
        foreach ($this->parseAcceptLanguage($request->headers->get('Accept-Language', '')) as $tag) {
            if ($supported === [] || in_array($tag, $supported, true)) {
                return $tag;
            }
        }

        return null;
    }

    /**
     * @param  array<int, string>  $supported
     */
    private function fromUser(Request $request, string $column, array $supported): ?string
    {
        $user = $request->user();

        if (! $user || ! isset($user->{$column}) || ! is_string($user->{$column}) || $user->{$column} === '') {
            return null;
        }

        $locale = $user->{$column};

        if ($supported !== [] && ! in_array($locale, $supported, true)) {
            return null;
        }

        return $locale;
    }

    /**
     * Parses an "Accept-Language" header into a list of base language tags
     * (e.g. "fr-CA;q=0.8" -> "fr"), ordered by descending quality.
     *
     * @return array<int, string>
     */
    private function parseAcceptLanguage(string $header): array
    {
        if ($header === '') {
            return [];
        }

        $tags = [];

        foreach (explode(',', $header) as $part) {
            $segments = explode(';q=', trim($part));
            $tag = trim($segments[0]);

            if ($tag === '' || $tag === '*') {
                continue;
            }

            $quality = isset($segments[1]) ? (float) $segments[1] : 1.0;
            $tags[] = ['tag' => strtolower(explode('-', $tag)[0]), 'q' => $quality];
        }

        usort($tags, fn (array $a, array $b) => $b['q'] <=> $a['q']);

        return array_values(array_unique(array_column($tags, 'tag')));
    }
}
