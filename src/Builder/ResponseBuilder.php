<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Builder;

use Bhry98\LaravelApiResponse\Contracts\ResponseBuilderContract;
use Bhry98\LaravelApiResponse\Data\ResponsePayload;
use Bhry98\LaravelApiResponse\Pipeline\ResponsePipeline;
use Bhry98\LaravelApiResponse\Presets\PresetManager;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

/**
 * The fluent, extensible response builder behind the api() helper and the
 * ApiResponse facade.
 *
 * @method static void macro(string $name, callable $macro)
 */
class ResponseBuilder implements ResponseBuilderContract
{
    use Concerns\BuildsResponse;
    use Concerns\HasBusinessResponses;
    use Concerns\HasDataDetection;
    use Concerns\HasFluentState;
    use Concerns\HasHttpResponses;
    use Concerns\HasTemplates;
    use Macroable;

    protected ResponsePayload $payload;

    public function __construct(
        protected readonly Repository $config,
        protected readonly ResponsePipeline $pipeline,
        protected readonly PresetManager $presets,
        protected readonly ResponseFactory $responseFactory,
        protected readonly Dispatcher $events,
    ) {
        $this->payload = new ResponsePayload(
            message: (string) $this->config->get('bhry98-api-response.messages.success', 'Operation successful.'),
            preset: (string) $this->config->get('bhry98-api-response.preset', 'default'),
        );
    }

    /**
     * Fresh, container-resolved builder instance. Prefer the `api()` helper
     * or the `ApiResponse` facade in application code.
     */
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * Resolves a message for a ready-made shortcut from
     * config('bhry98-api-response.messages'). For locale-driven messages,
     * chain ->localize('bhry98-api-response::api.{$key}') explicitly - the
     * package ships lang/en/api.php (publishable) for exactly that purpose.
     * Config always wins here so a config override is never silently
     * shadowed by a shipped default translation.
     */
    protected function defaultMessage(string $key): string
    {
        return (string) $this->config->get(
            "bhry98-api-response.messages.{$key}",
            Str::headline($key),
        );
    }
}
