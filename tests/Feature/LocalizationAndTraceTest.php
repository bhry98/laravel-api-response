<?php

declare(strict_types=1);

it('leaves the message untouched when no translation exists', function () {
    $result = api()->success()->message('some.missing.key')->localize()->build();

    expect($result['message'])->toBe('some.missing.key');
});

it('swaps in the translated message when a translation exists', function () {
    app('translator')->addLines(['api.greeting' => 'Bonjour!'], 'en');

    $result = api()->success()->localize('api.greeting')->build();

    expect($result['message'])->toBe('Bonjour!');
});

it('lets a config override win over the shipped package translation', function () {
    app('translator')->addLines(['bhry98-api-response::api.not_found' => 'Should not win.'], 'en');
    config(['bhry98-api-response.messages.not_found' => 'Nothing here.']);

    expect(api()->notFound()->build()['message'])->toBe('Nothing here.');
});

it('can still pull a shortcut message from the package translation namespace via localize()', function () {
    $result = api()->updated()->localize('bhry98-api-response::api.updated')->build();

    expect($result['message'])->toBe('Updated successfully.');
});

it('never attaches a trace unless allow_trace or app.debug is enabled', function () {
    config(['bhry98-api-response.allow_trace' => false]);
    config(['app.debug' => false]);

    $result = api()->fail()->trace(new Exception('boom'))->build();

    expect($result)->not->toHaveKey('debug');
});

it('attaches a trace when allow_trace is enabled', function () {
    config(['bhry98-api-response.allow_trace' => true]);

    $result = api()->fail()->trace(new Exception('boom'))->build();

    expect($result['debug']['exception']['message'])->toBe('boom');
});
