<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Support;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\ResponseFactory;

/**
 * Wraps response()->json($data) in the package's standard envelope, but only
 * when config('bhry98-api-response.override_default_response') is true at
 * call time - checked dynamically so the flag can be toggled at runtime
 * (e.g. per-environment or in tests) without re-wiring the container.
 */
class OverriddenResponseFactory extends ResponseFactory
{
    public function __construct(
        Factory $view,
        Redirector $redirector,
        private readonly Repository $config,
    ) {
        parent::__construct($view, $redirector);
    }

    public function json($data = [], $status = 200, array $headers = [], $options = 0)
    {
        if (! $this->config->get('bhry98-api-response.override_default_response', false)) {
            return parent::json($data, $status, $headers, $options);
        }

        $keys = $this->config->get('bhry98-api-response.keys', []);

        $envelope = [
            ($keys['status'] ?? 'status') => $status < 400,
            ($keys['data'] ?? 'data') => $data,
        ];

        return parent::json($envelope, $status, $headers, $options);
    }
}
