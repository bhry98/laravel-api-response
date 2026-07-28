<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Facades;

use Bhry98\LaravelApiResponse\Builder\ResponseBuilder;
use Bhry98\LaravelApiResponse\Hooks\HookRegistry;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Facade;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * The main entry point for building API responses.
 *
 * @method static ResponseBuilder success(bool $success = true)
 * @method static ResponseBuilder fail()
 * @method static ResponseBuilder message(string $message)
 * @method static ResponseBuilder data(mixed $data)
 * @method static ResponseBuilder meta(array $meta)
 * @method static ResponseBuilder links(array $links)
 * @method static ResponseBuilder errors(array $errors)
 * @method static ResponseBuilder extra(array $extra)
 * @method static ResponseBuilder statusCode(int $code)
 * @method static ResponseBuilder headers(array $headers)
 * @method static ResponseBuilder cookie(Cookie $cookie)
 * @method static ResponseBuilder cookies(array $cookies)
 * @method static ResponseBuilder preset(string $preset)
 * @method static ResponseBuilder version(string $version)
 * @method static ResponseBuilder localize(?string $langKey = null)
 * @method static ResponseBuilder trace(\Throwable $exception)
 * @method static ResponseBuilder ok(mixed $data = null, ?string $message = null)
 * @method static ResponseBuilder created(mixed $data = null, ?string $message = null)
 * @method static ResponseBuilder accepted(mixed $data = null, ?string $message = null)
 * @method static ResponseBuilder noContent()
 * @method static ResponseBuilder badRequest(?string $message = null, array $errors = [])
 * @method static ResponseBuilder unauthorized(?string $message = null)
 * @method static ResponseBuilder forbidden(?string $message = null)
 * @method static ResponseBuilder notFound(?string $message = null)
 * @method static ResponseBuilder conflict(?string $message = null)
 * @method static ResponseBuilder unprocessable(array $errors = [], ?string $message = null)
 * @method static ResponseBuilder tooManyRequests(?string $message = null)
 * @method static ResponseBuilder internal(?string $message = null)
 * @method static ResponseBuilder serviceUnavailable(?string $message = null)
 * @method static ResponseBuilder saved(mixed $data = null, ?string $message = null)
 * @method static ResponseBuilder updated(mixed $data = null, ?string $message = null)
 * @method static ResponseBuilder deleted(?string $message = null)
 * @method static ResponseBuilder restored(mixed $data = null, ?string $message = null)
 * @method static ResponseBuilder archived(mixed $data = null, ?string $message = null)
 * @method static ResponseBuilder verified(mixed $data = null, ?string $message = null)
 * @method static ResponseBuilder registered(mixed $data = null, ?string $message = null)
 * @method static ResponseBuilder loggedIn(mixed $data = null, ?string $message = null)
 * @method static ResponseBuilder loggedOut(?string $message = null)
 * @method static ResponseBuilder passwordChanged(?string $message = null)
 * @method static ResponseBuilder uploaded(mixed $data = null, ?string $message = null)
 * @method static ResponseBuilder download(mixed $data = null, ?string $message = null)
 * @method static ResponseBuilder emailSent(?string $message = null)
 * @method static ResponseBuilder otpSent(?string $message = null)
 * @method static ResponseBuilder from(mixed $data, ?string $message = null)
 * @method static ResponseBuilder paginate(mixed $paginator, ?string $message = null)
 * @method static ResponseBuilder resource(mixed $resource, ?string $message = null)
 * @method static ResponseBuilder resourceCollection(mixed $collection, ?string $message = null)
 * @method static ResponseBuilder template(string $name, array $overrides = [])
 * @method static array build()
 * @method static array toArray()
 * @method static JsonResponse send()
 * @method static void macro(string $name, callable $macro)
 * @method static void before(Closure $callback)
 * @method static void after(Closure $callback)
 *
 * @see ResponseBuilder
 */
class ApiResponse extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ResponseBuilder::class;
    }

    /**
     * Register a hook that runs before the response payload is formatted.
     */
    public static function before(Closure $callback): void
    {
        app(HookRegistry::class)->before($callback);
    }

    /**
     * Register a hook that runs after the response payload is assembled,
     * still before formatting/sending.
     */
    public static function after(Closure $callback): void
    {
        app(HookRegistry::class)->after($callback);
    }

    /**
     * Facades resolve a fresh instance every call since ResponseBuilder is
     * stateful/fluent and must never be shared between responses.
     */
    protected static function resolveFacadeInstance($name)
    {
        return static::$app->make($name);
    }
}
