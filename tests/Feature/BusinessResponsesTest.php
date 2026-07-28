<?php

declare(strict_types=1);

it('exposes business-shaped shortcuts with sensible defaults', function (string $method, int $status) {
    $response = api()->{$method}();

    expect($response->build()['status'])->toBeTrue();

    expect(api()->{$method}()->send()->getStatusCode())->toBe($status);
})->with([
    ['saved', 200],
    ['updated', 200],
    ['deleted', 200],
    ['restored', 200],
    ['archived', 200],
    ['verified', 200],
    ['registered', 201],
    ['loggedIn', 200],
    ['loggedOut', 200],
    ['passwordChanged', 200],
    ['uploaded', 201],
    ['download', 200],
    ['emailSent', 200],
    ['otpSent', 200],
]);

it('uses the configured message for business responses', function () {
    config(['bhry98-api-response.messages.updated' => 'Record updated.']);

    expect(api()->updated()->build()['message'])->toBe('Record updated.');
});

it('carries data on data-bearing business shortcuts', function () {
    expect(api()->saved(['id' => 1])->build()['data'])->toBe(['id' => 1]);
    expect(api()->registered(['id' => 2])->build()['data'])->toBe(['id' => 2]);
});
