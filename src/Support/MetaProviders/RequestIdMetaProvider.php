<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Support\MetaProviders;

use Bhry98\LaravelApiResponse\Contracts\MetaProviderContract;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RequestIdMetaProvider implements MetaProviderContract
{
    public function __construct(
        private readonly Request $request,
    ) {}

    public function key(): string
    {
        return 'request_id';
    }

    public function value(): mixed
    {
        $existing = $this->request->attributes->get('api_response_request_id')
            ?? $this->request->headers->get('X-Request-Id');

        if ($existing) {
            return $existing;
        }

        $id = (string) Str::uuid();
        $this->request->attributes->set('api_response_request_id', $id);

        return $id;
    }
}
