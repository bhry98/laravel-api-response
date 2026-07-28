<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Contracts;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

/**
 * The core, swappable contract behind the api() helper / ApiResponse facade.
 *
 * Only the cross-cutting fluent state and terminal methods live here.
 * HTTP-status shortcuts (ok, created, ...) and business shortcuts
 * (saved, updated, ...) are concrete methods on ResponseBuilder itself so
 * they remain fully autocompletable without bloating this contract.
 */
interface ResponseBuilderContract extends Responsable
{
    public function success(bool $success = true): static;

    public function fail(): static;

    public function message(string $message): static;

    public function data(mixed $data): static;

    public function meta(array $meta): static;

    public function links(array $links): static;

    public function errors(array $errors): static;

    public function extra(array $extra): static;

    public function statusCode(int $code): static;

    public function headers(array $headers): static;

    public function preset(string $preset): static;

    public function version(string $version): static;

    /**
     * @return array<string, mixed>
     */
    public function build(): array;

    public function send(): JsonResponse;
}
