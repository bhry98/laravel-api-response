<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Providers;

use Bhry98\LaravelApiResponse\Builder\ResponseBuilder;
use Bhry98\LaravelApiResponse\Contracts\DataResolverContract;
use Bhry98\LaravelApiResponse\Exceptions\ExceptionRenderer;
use Bhry98\LaravelApiResponse\Hooks\HookRegistry;
use Bhry98\LaravelApiResponse\Middleware\ApiRateLimit;
use Bhry98\LaravelApiResponse\Middleware\AuditRequests;
use Bhry98\LaravelApiResponse\Middleware\ResolveLocale;
use Bhry98\LaravelApiResponse\Presets\PresetManager;
use Bhry98\LaravelApiResponse\Responses\ErrorResponse;
use Bhry98\LaravelApiResponse\Responses\InternalErrorResponse;
use Bhry98\LaravelApiResponse\Responses\NotFoundResponse;
use Bhry98\LaravelApiResponse\Responses\SuccessResponse;
use Bhry98\LaravelApiResponse\Responses\ValidationErrorResponse;
use Bhry98\LaravelApiResponse\Support\DataResolver;
use Bhry98\LaravelApiResponse\Support\MetaProviderRegistry;
use Bhry98\LaravelApiResponse\Support\OverriddenResponseFactory;
use Bhry98\LaravelApiResponse\Testing\TestResponseMacros;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Factory as ViewFactory;
use Throwable;

class BResponseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/bhry98-api-response.php', 'bhry98-api-response');

        $this->app->singleton(HookRegistry::class);
        $this->app->singleton(PresetManager::class);
        $this->app->singleton(MetaProviderRegistry::class);
        $this->app->bind(DataResolverContract::class, DataResolver::class);

        // ResponseBuilder itself is intentionally left unbound: it is fluent
        // and stateful, so it must be resolved fresh (via container
        // autowiring) every single time - never as a singleton.

        // Legacy (< v2) bindings - kept for backward compatibility.
        $this->app->bind('bResponse.success', fn () => new SuccessResponse);
        $this->app->bind('bResponse.error', fn () => new ErrorResponse);
        $this->app->bind('bResponse.validationError', fn () => new ValidationErrorResponse);
        $this->app->bind('bResponse.notFound', fn () => new NotFoundResponse);
        $this->app->bind('bResponse.internalError', fn () => new InternalErrorResponse);

        // Always wraps the binding; OverriddenResponseFactory itself checks
        // config('bhry98-api-response.override_default_response') on every
        // call, so the flag stays fully opt-in and toggleable at runtime.
        $this->app->extend(ResponseFactory::class, fn () => new OverriddenResponseFactory(
            $this->app->make(ViewFactory::class),
            $this->app->make(Redirector::class),
            $this->app->make('config'),
        ));
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/bhry98-api-response.php' => config_path('bhry98-api-response.php'),
        ], 'bhry98-api-response-config');

        $this->publishes([
            __DIR__.'/../../resources/lang' => $this->app->langPath('vendor/bhry98-api-response'),
        ], 'bhry98-api-response-lang');

        $this->publishesMigrations([
            __DIR__.'/../../database/migrations' => database_path('migrations'),
        ], 'bhry98-api-response-migrations');

        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'bhry98-api-response');

        $this->registerMiddlewareAliases();

        TestResponseMacros::register();

        // BC demo macro, mirrors the pre-2.0 `SuccessResponse::macro('paginated', ...)`.
        // Macroable::__call rebinds $this to the ResponseBuilder instance at
        // call time via Closure::bindTo() - the @var documents that runtime
        // type for IDEs/PHPStan even though it differs from this closure's
        // lexical scope (the service provider).
        ResponseBuilder::macro('paginated', function (array $meta) {
            /** @var ResponseBuilder $this */
            return $this->meta($meta);
        });

        // Always registered; ExceptionRenderer::shouldHandle() checks
        // config('...override_exception_handler') dynamically so the flag
        // stays opt-in and toggleable at runtime.
        $this->registerExceptionRenderer();
    }

    /**
     * All opt-in - none of these are attached to any route/group unless the
     * developer explicitly adds the alias.
     */
    private function registerMiddlewareAliases(): void
    {
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('api.rate_limit', ApiRateLimit::class);
        $router->aliasMiddleware('api.audit', AuditRequests::class);
        $router->aliasMiddleware('api.locale', ResolveLocale::class);
    }

    private function registerExceptionRenderer(): void
    {
        $register = function ($handler): void {
            if (! method_exists($handler, 'renderable')) {
                return;
            }

            $handler->renderable(function (Throwable $e, Request $request) {
                $renderer = $this->app->make(ExceptionRenderer::class);

                if (! $renderer->shouldHandle($request, $e)) {
                    return null;
                }

                return $renderer->render($this->app->make(ResponseBuilder::class), $e);
            });
        };

        // afterResolving() never fires for a singleton that was already
        // resolved before this provider booted, so cover that case too.
        if ($this->app->resolved(ExceptionHandler::class)) {
            $register($this->app->make(ExceptionHandler::class));

            return;
        }

        $this->app->afterResolving(ExceptionHandler::class, $register);
    }
}
