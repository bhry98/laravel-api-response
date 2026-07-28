<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Builder\Concerns;

/**
 * One method per common HTTP response, each pinning the correct status code
 * and a configurable default message (config('bhry98-api-response.messages')).
 */
trait HasHttpResponses
{
    public function ok(mixed $data = null, ?string $message = null): static
    {
        return $this->success()->statusCode(200)->message($message ?? $this->defaultMessage('success'))->data($data);
    }

    public function created(mixed $data = null, ?string $message = null): static
    {
        return $this->success()->statusCode(201)->message($message ?? $this->defaultMessage('created'))->data($data);
    }

    public function accepted(mixed $data = null, ?string $message = null): static
    {
        return $this->success()->statusCode(202)->message($message ?? $this->defaultMessage('accepted'))->data($data);
    }

    public function noContent(): static
    {
        return $this->success()->statusCode(204)->message('');
    }

    public function badRequest(?string $message = null, array $errors = []): static
    {
        return $this->fail()->statusCode(400)->message($message ?? $this->defaultMessage('bad_request'))->errors($errors);
    }

    public function unauthorized(?string $message = null): static
    {
        return $this->fail()->statusCode(401)->message($message ?? $this->defaultMessage('unauthorized'));
    }

    public function forbidden(?string $message = null): static
    {
        return $this->fail()->statusCode(403)->message($message ?? $this->defaultMessage('forbidden'));
    }

    public function notFound(?string $message = null): static
    {
        return $this->fail()->statusCode(404)->message($message ?? $this->defaultMessage('not_found'));
    }

    public function conflict(?string $message = null): static
    {
        return $this->fail()->statusCode(409)->message($message ?? $this->defaultMessage('conflict'));
    }

    public function unprocessable(array $errors = [], ?string $message = null): static
    {
        return $this->fail()->statusCode(422)->message($message ?? $this->defaultMessage('validation'))->errors($errors);
    }

    public function tooManyRequests(?string $message = null): static
    {
        return $this->fail()->statusCode(429)->message($message ?? $this->defaultMessage('too_many_requests'));
    }

    public function internal(?string $message = null): static
    {
        return $this->fail()->statusCode(500)->message($message ?? $this->defaultMessage('internal'));
    }

    public function serviceUnavailable(?string $message = null): static
    {
        return $this->fail()->statusCode(503)->message($message ?? $this->defaultMessage('service_unavailable'));
    }
}
