<?php

namespace Bhry98\LaravelApiResponse\Providers;

use Bhry98\LaravelApiResponse\Responses\{
    SuccessResponse,
    ErrorResponse,
    ValidationErrorResponse,
    NotFoundResponse,
    InternalErrorResponse
};
use Illuminate\Support\ServiceProvider;


class BResponseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/bhry98-api-response.php', 'bhry98-api-response');

        // Bind each response as a concrete class (not singletons so make() returns fresh instances)
        $this->app->bind('bResponse.success', fn() => new SuccessResponse());
        $this->app->bind('bResponse.error', fn() => new ErrorResponse());
        $this->app->bind('bResponse.validationError', fn() => new ValidationErrorResponse());
        $this->app->bind('bResponse.notFound', fn() => new NotFoundResponse());
        $this->app->bind('bResponse.internalError', fn() => new InternalErrorResponse());
    }

    public function boot(): void
    {
        // publish config
        $this->publishes([
            __DIR__ . '/../config/bhry98-api-response.php' => config_path('bhry98-api-response.php'),
        ], 'config');

        // Example of registering macros developers might want:
        // e.g., add success()->paginate() macro to SuccessResponse
        if (class_exists(SuccessResponse::class)) {
            SuccessResponse::macro('paginated', function (array $paginationMeta) {
                /** @var SuccessResponse $this */
                $this->meta($paginationMeta);
                return $this;
            });
        }
    }
}
