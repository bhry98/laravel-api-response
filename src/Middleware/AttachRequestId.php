<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Optional middleware: ensures every request carries a stable request id
 * (reusing an incoming X-Request-Id header when present) that the
 * "request_id" meta provider will then reuse, and echoes it back as a
 * response header. Not registered automatically - add it to your route
 * middleware group if you want it.
 */
class AttachRequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->headers->get('X-Request-Id') ?: (string) Str::uuid();
        $request->attributes->set('api_response_request_id', $id);

        /** @var Response $response */
        $response = $next($request);
        $response->headers->set('X-Request-Id', $id);

        return $response;
    }
}
