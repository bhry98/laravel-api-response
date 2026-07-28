<?php

declare(strict_types=1);

it('never includes debug info when app.debug is false, even if enabled', function () {
    config(['bhry98-api-response.debug.enabled' => true]);
    config(['app.debug' => false]);

    $result = api()->success()->build();

    expect($result)->not->toHaveKey('debug');
});

it('never includes debug info when debug.enabled is false, even in app.debug', function () {
    config(['bhry98-api-response.debug.enabled' => false]);
    config(['app.debug' => true]);

    $result = api()->success()->build();

    expect($result)->not->toHaveKey('debug');
});

it('includes the configured debug diagnostics when both flags are true', function () {
    config(['bhry98-api-response.debug.enabled' => true]);
    config(['app.debug' => true]);
    config(['bhry98-api-response.debug.include' => ['time', 'memory']]);

    $result = api()->success()->build();

    expect($result['debug'])->toHaveKeys(['execution_time_ms', 'memory_usage_mb']);
});
