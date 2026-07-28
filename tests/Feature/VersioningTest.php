<?php

declare(strict_types=1);

it('uses the configured default api version', function () {
    config(['bhry98-api-response.version' => 'v1']);
    config(['bhry98-api-response.meta.enabled' => ['api_version']]);

    expect(api()->success()->build()['meta']['api_version'])->toBe('v1');
});

it('allows overriding the version per response', function () {
    config(['bhry98-api-response.meta.enabled' => ['api_version']]);

    $result = api()->version('v2')->success()->build();

    expect($result['meta']['api_version'])->toBe('v2');
});
