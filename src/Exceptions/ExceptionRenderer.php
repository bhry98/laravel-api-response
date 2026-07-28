<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Exceptions;

use Bhry98\LaravelApiResponse\Builder\ResponseBuilder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Optional exception -> api() response translation. Only invoked by the
 * service provider when config('bhry98-api-response.exceptions.override_exception_handler')
 * is true.
 */
class ExceptionRenderer
{
    public function __construct(
        private readonly Repository $config,
    ) {}

    public function shouldHandle(Request $request, Throwable $e): bool
    {
        if (! $this->config->get('bhry98-api-response.exceptions.override_exception_handler', false)) {
            return false;
        }

        return $request->expectsJson() || $request->is('api/*');
    }

    public function render(ResponseBuilder $builder, Throwable $e): JsonResponse
    {
        return match (true) {
            $e instanceof ValidationException => $builder->unprocessable($e->errors(), $e->getMessage())->send(),
            $e instanceof AuthenticationException => $builder->unauthorized($e->getMessage())->send(),
            $e instanceof AuthorizationException => $builder->forbidden($e->getMessage())->send(),
            $e instanceof ModelNotFoundException => $builder->notFound()->send(),
            $e instanceof HttpExceptionInterface => $builder->fail()
                ->statusCode($e->getStatusCode())
                ->message($e->getMessage() !== '' ? $e->getMessage() : $this->config->get('bhry98-api-response.messages.error', 'An error occurred.'))
                ->send(),
            default => $builder->trace($e)
                ->internal($this->config->get('app.debug') ? $e->getMessage() : null)
                ->send(),
        };
    }
}
