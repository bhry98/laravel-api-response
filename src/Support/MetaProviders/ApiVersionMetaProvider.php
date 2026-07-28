<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Support\MetaProviders;

use Bhry98\LaravelApiResponse\Contracts\MetaProviderContract;
use Illuminate\Contracts\Config\Repository;

class ApiVersionMetaProvider implements MetaProviderContract
{
    public function __construct(
        private readonly Repository $config,
    ) {}

    public function key(): string
    {
        return 'api_version';
    }

    public function value(): mixed
    {
        return $this->config->get('bhry98-api-response.version', 'v1');
    }
}
