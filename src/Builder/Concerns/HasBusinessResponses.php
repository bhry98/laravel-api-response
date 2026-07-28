<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Builder\Concerns;

/**
 * Domain-shaped shortcuts for the responses almost every API ends up
 * writing by hand. Messages come from config('bhry98-api-response.messages').
 */
trait HasBusinessResponses
{
    public function saved(mixed $data = null, ?string $message = null): static
    {
        return $this->success()->statusCode(200)->message($message ?? $this->defaultMessage('saved'))->data($data);
    }

    public function updated(mixed $data = null, ?string $message = null): static
    {
        return $this->success()->statusCode(200)->message($message ?? $this->defaultMessage('updated'))->data($data);
    }

    public function deleted(?string $message = null): static
    {
        return $this->success()->statusCode(200)->message($message ?? $this->defaultMessage('deleted'));
    }

    public function restored(mixed $data = null, ?string $message = null): static
    {
        return $this->success()->statusCode(200)->message($message ?? $this->defaultMessage('restored'))->data($data);
    }

    public function archived(mixed $data = null, ?string $message = null): static
    {
        return $this->success()->statusCode(200)->message($message ?? $this->defaultMessage('archived'))->data($data);
    }

    public function verified(mixed $data = null, ?string $message = null): static
    {
        return $this->success()->statusCode(200)->message($message ?? $this->defaultMessage('verified'))->data($data);
    }

    public function registered(mixed $data = null, ?string $message = null): static
    {
        return $this->success()->statusCode(201)->message($message ?? $this->defaultMessage('registered'))->data($data);
    }

    public function loggedIn(mixed $data = null, ?string $message = null): static
    {
        return $this->success()->statusCode(200)->message($message ?? $this->defaultMessage('logged_in'))->data($data);
    }

    public function loggedOut(?string $message = null): static
    {
        return $this->success()->statusCode(200)->message($message ?? $this->defaultMessage('logged_out'));
    }

    public function passwordChanged(?string $message = null): static
    {
        return $this->success()->statusCode(200)->message($message ?? $this->defaultMessage('password_changed'));
    }

    public function uploaded(mixed $data = null, ?string $message = null): static
    {
        return $this->success()->statusCode(201)->message($message ?? $this->defaultMessage('uploaded'))->data($data);
    }

    public function download(mixed $data = null, ?string $message = null): static
    {
        return $this->success()->statusCode(200)->message($message ?? $this->defaultMessage('downloaded'))->data($data);
    }

    public function emailSent(?string $message = null): static
    {
        return $this->success()->statusCode(200)->message($message ?? $this->defaultMessage('email_sent'));
    }

    public function otpSent(?string $message = null): static
    {
        return $this->success()->statusCode(200)->message($message ?? $this->defaultMessage('otp_sent'));
    }
}
