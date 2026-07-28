<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Builder\Concerns;

use Symfony\Component\HttpFoundation\Cookie;
use Throwable;

/**
 * The cross-cutting fluent setters every response (regardless of shortcut
 * used) shares: message, data, meta, errors, headers, cookies, status...
 */
trait HasFluentState
{
    public function success(bool $success = true): static
    {
        $this->payload->success = $success;

        return $this;
    }

    public function fail(): static
    {
        return $this->success(false);
    }

    public function message(string $message): static
    {
        $this->payload->message = $message;

        return $this;
    }

    public function data(mixed $data): static
    {
        $this->payload->data = $data;

        return $this;
    }

    public function meta(array $meta): static
    {
        $this->payload->withMeta($meta);

        return $this;
    }

    public function links(array $links): static
    {
        $this->payload->withLinks($links);

        return $this;
    }

    public function errors(array $errors): static
    {
        $this->payload->errors = [...$this->payload->errors, ...$errors];

        return $this;
    }

    /**
     * Extra top-level keys merged into the final envelope alongside
     * status/message/data (e.g. ->extra(['request_id' => uniqid()])).
     */
    public function extra(array $extra): static
    {
        $this->payload->extra = [...$this->payload->extra, ...$extra];

        return $this;
    }

    public function statusCode(int $code): static
    {
        $this->payload->statusCode = $code;

        return $this;
    }

    /**
     * @param  array<string, string>  $headers
     */
    public function headers(array $headers): static
    {
        $this->payload->headers = [...$this->payload->headers, ...$headers];

        return $this;
    }

    public function cookie(Cookie $cookie): static
    {
        $this->payload->cookies[] = $cookie;

        return $this;
    }

    /**
     * @param  array<int, Cookie>|array<string, string>  $cookies  Either Cookie instances or name => value pairs.
     */
    public function cookies(array $cookies): static
    {
        foreach ($cookies as $key => $cookie) {
            if ($cookie instanceof Cookie) {
                $this->payload->cookies[] = $cookie;
            } elseif (is_string($key)) {
                $this->payload->cookies[] = Cookie::create($key)->withValue((string) $cookie);
            }
        }

        return $this;
    }

    public function preset(string $preset): static
    {
        $this->payload->preset = $preset;

        return $this;
    }

    public function version(string $version): static
    {
        $this->payload->version = $version;

        return $this;
    }

    /**
     * Translate the current (or given) message key through Laravel's translator.
     */
    public function localize(?string $langKey = null): static
    {
        $key = $langKey ?? $this->payload->message;
        $translated = __($key);

        if ($translated !== $key) {
            $this->payload->message = $translated;
        }

        return $this;
    }

    /**
     * BC helper: attach exception details into the debug payload when
     * trace inclusion is allowed (config('allow_trace') or app.debug).
     */
    public function trace(Throwable $exception): static
    {
        $allowed = $this->config->get('bhry98-api-response.allow_trace', false)
            || $this->config->get('app.debug', false);

        if (! $allowed) {
            return $this;
        }

        $this->payload->debug = [
            ...($this->payload->debug ?? []),
            'exception' => [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile().':'.$exception->getLine(),
                'trace' => collect($exception->getTrace())->take(20)->map(fn (array $frame) => [
                    'file' => $frame['file'] ?? null,
                    'line' => $frame['line'] ?? null,
                    'function' => $frame['function'],
                    'class' => $frame['class'] ?? null,
                ])->all(),
            ],
        ];

        return $this;
    }
}
