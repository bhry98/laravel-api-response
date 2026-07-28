<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Presets;

use Bhry98\LaravelApiResponse\Contracts\PresetContract;
use Bhry98\LaravelApiResponse\Contracts\ResponseFormatterContract;
use Bhry98\LaravelApiResponse\Exceptions\PresetNotFoundException;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;

/**
 * Registry of named presets. Built-ins are loaded from
 * config('bhry98-api-response.presets'); register your own at runtime with
 * PresetManager::register() (e.g. from a service provider's boot method).
 */
class PresetManager
{
    /** @var array<string, class-string<PresetContract>|PresetContract> */
    private array $presets = [];

    public function __construct(
        private readonly Application $app,
        Repository $config,
    ) {
        $this->presets = $config->get('bhry98-api-response.presets', []);
    }

    public function register(string $name, string|PresetContract $preset): void
    {
        $this->presets[$name] = $preset;
    }

    public function has(string $name): bool
    {
        return isset($this->presets[$name]);
    }

    public function resolve(string $name): PresetContract
    {
        $preset = $this->presets[$name] ?? throw PresetNotFoundException::make($name);

        return is_string($preset) ? $this->app->make($preset) : $preset;
    }

    public function formatterFor(string $name): ResponseFormatterContract
    {
        return $this->resolve($name)->formatter();
    }
}
