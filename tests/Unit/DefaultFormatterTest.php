<?php

declare(strict_types=1);

use Bhry98\LaravelApiResponse\Data\ResponsePayload;
use Bhry98\LaravelApiResponse\Formatters\DefaultFormatter;

it('omits errors, meta and links when empty', function () {
    $formatter = app(DefaultFormatter::class);

    $result = $formatter->format(new ResponsePayload(success: true, message: 'Hi'));

    expect($result)->toBe(['status' => true, 'message' => 'Hi', 'data' => null]);
});

it('includes errors, meta and links only when present', function () {
    $formatter = app(DefaultFormatter::class);

    $result = $formatter->format(new ResponsePayload(
        success: false,
        message: 'Nope',
        errors: ['field' => ['bad']],
        meta: ['a' => 1],
        links: ['self' => '/x'],
    ));

    expect($result)->toHaveKeys(['status', 'message', 'data', 'errors', 'meta', 'links']);
});
