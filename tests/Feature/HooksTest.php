<?php

declare(strict_types=1);

use Bhry98\LaravelApiResponse\Data\ResponsePayload;
use Bhry98\LaravelApiResponse\Facades\ApiResponse;
use Bhry98\LaravelApiResponse\Hooks\HookRegistry;

afterEach(function () {
    app(HookRegistry::class)->flush();
});

it('runs before hooks and allows mutating the payload', function () {
    ApiResponse::before(function (ResponsePayload $payload) {
        $payload->extra['injected'] = 'before';
    });

    $result = api()->success()->build();

    expect($result['injected'])->toBe('before');
});

it('runs after hooks after before hooks and global meta have applied', function () {
    $order = [];

    ApiResponse::before(function () use (&$order) {
        $order[] = 'before';
    });

    ApiResponse::after(function () use (&$order) {
        $order[] = 'after';
    });

    api()->success()->build();

    expect($order)->toBe(['before', 'after']);
});
