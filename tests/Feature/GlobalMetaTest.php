<?php

declare(strict_types=1);

it('attaches no automatic meta by default', function () {
    $result = api()->success()->build();

    expect($result)->not->toHaveKey('meta');
});

it('attaches configured global meta providers', function () {
    config(['bhry98-api-response.meta.enabled' => ['api_version', 'locale']]);
    config(['bhry98-api-response.version' => 'v3']);

    $result = api()->success()->build();

    expect($result['meta'])->toMatchArray([
        'api_version' => 'v3',
        'locale' => 'en',
    ]);
});

it('lets explicit meta win over global meta on key collisions', function () {
    config(['bhry98-api-response.meta.enabled' => ['api_version']]);
    config(['bhry98-api-response.version' => 'v3']);

    $result = api()->success()->meta(['api_version' => 'overridden'])->build();

    expect($result['meta']['api_version'])->toBe('overridden');
});

it('includes a request id when enabled', function () {
    config(['bhry98-api-response.meta.enabled' => ['request_id']]);

    $result = api()->success()->build();

    expect($result['meta']['request_id'])->toBeString()->not->toBeEmpty();
});
