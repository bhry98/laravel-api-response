<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Support;

use Bhry98\LaravelApiResponse\Contracts\MetaProviderContract;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;

/**
 * Resolves the "enabled" global meta providers listed in
 * config('bhry98-api-response.meta.enabled') and collects their values.
 *
 * @property-read Application $app
 */
class MetaProviderRegistry
{
    /** @var array<string, class-string<MetaProviderContract>> */
    protected array $providers;

    public function __construct(
        private readonly Application $app,
        private readonly Repository $config,
    ) {
        $this->providers = $this->config->get('bhry98-api-response.meta.providers', []);
    }

    /**
     * Register (or override) a meta provider under the given key.
     *
     * @param  class-string<MetaProviderContract>  $providerClass
     */
    public function register(string $key, string $providerClass): void
    {
        $this->providers[$key] = $providerClass;
    }

    /**
     * @return array<string, mixed>
     */
    public function collect(): array
    {
        $enabled = $this->config->get('bhry98-api-response.meta.enabled', []);

        $values = [];

        foreach ($enabled as $key) {
            $providerClass = $this->providers[$key] ?? null;

            if ($providerClass === null || ! class_exists($providerClass)) {
                continue;
            }

            /** @var MetaProviderContract $provider */
            $provider = $this->app->make($providerClass);
            $values[$provider->key()] = $provider->value();
        }

        return $values;
    }
}
