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
 *
 * Status codes come from config('bhry98-api-response.exceptions.map'):
 * every built-in exception type can be remapped there, and any extra
 * exception class listed with a status code is rendered as a failed api()
 * response with that code (a `null` code means "use the exception's own
 * status code", which only HttpExceptionInterface provides).
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
        $e = $this->unwrap($e);
        $mapped = $this->mappedStatus($e);

        return match (true) {
            $e instanceof ValidationException => $builder
                ->unprocessable($e->errors(), $e->getMessage())
                // An explicit ->status() on the exception beats the map.
                ->statusCode($e->status !== 422 ? $e->status : ($mapped ?? 422))
                ->send(),
            $e instanceof AuthenticationException => $builder
                ->unauthorized($e->getMessage() !== '' ? $e->getMessage() : null)
                ->statusCode($mapped ?? 401)
                ->send(),
            $e instanceof AuthorizationException => $builder
                ->forbidden($e->getMessage() !== '' ? $e->getMessage() : null)
                // Gate::denyWithStatus()/denyAsNotFound() beats the map.
                ->statusCode($e->hasStatus() ? (int) $e->status() : ($mapped ?? 403))
                ->send(),
            // Never echo "No query results for model [App\Models\User] 5" back to the client.
            $e instanceof ModelNotFoundException => $builder
                ->notFound()
                ->statusCode($mapped ?? 404)
                ->send(),
            $e instanceof HttpExceptionInterface => $builder->fail()
                ->statusCode($mapped ?? $e->getStatusCode())
                ->message($e->getMessage() !== '' ? $e->getMessage() : $this->defaultErrorMessage())
                ->headers($this->normalizeHeaders($e->getHeaders()))
                ->send(),
            $mapped !== null => $builder->fail()
                ->statusCode($mapped)
                ->message($e->getMessage() !== '' ? $e->getMessage() : $this->defaultErrorMessage())
                ->send(),
            default => $builder->trace($e)
                ->internal($this->config->get('app.debug') ? $e->getMessage() : null)
                ->send(),
        };
    }

    /**
     * Laravel's exception handler converts some exceptions *before* render
     * callbacks run (ModelNotFoundException -> NotFoundHttpException,
     * AuthorizationException -> AccessDeniedHttpException/HttpException).
     * Recover the original so it gets its dedicated, safe treatment.
     */
    private function unwrap(Throwable $e): Throwable
    {
        $previous = $e->getPrevious();

        if ($e instanceof HttpExceptionInterface
            && ($previous instanceof ModelNotFoundException || $previous instanceof AuthorizationException)) {
            return $previous;
        }

        return $e;
    }

    private function mappedStatus(Throwable $e): ?int
    {
        foreach ((array) $this->config->get('bhry98-api-response.exceptions.map', []) as $class => $status) {
            if (is_string($class) && $e instanceof $class) {
                return $status === null ? null : (int) $status;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $headers
     * @return array<string, string>
     */
    private function normalizeHeaders(array $headers): array
    {
        $normalized = [];

        foreach ($headers as $key => $value) {
            $normalized[(string) $key] = is_array($value) ? implode(', ', $value) : (string) $value;
        }

        return $normalized;
    }

    private function defaultErrorMessage(): string
    {
        return (string) $this->config->get('bhry98-api-response.messages.error', 'An error occurred.');
    }
}
