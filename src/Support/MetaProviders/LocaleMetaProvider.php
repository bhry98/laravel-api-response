<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Support\MetaProviders;

use Bhry98\LaravelApiResponse\Contracts\MetaProviderContract;
use Illuminate\Contracts\Foundation\Application;

class LocaleMetaProvider implements MetaProviderContract
{
    public function __construct(
        private readonly Application $app,
    ) {}

    public function key(): string
    {
        return 'locale';
    }

    public function value(): mixed
    {
        return $this->app->getLocale();
    }
}
