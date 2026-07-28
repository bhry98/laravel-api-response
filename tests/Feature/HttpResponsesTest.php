<?php

declare(strict_types=1);

it('exposes one method per common http response with the correct status code', function (string $method, int $status, bool $success) {
    $response = api()->{$method}();

    expect($response->build()['status'])->toBe($success);
    expect($response->build())->toBeArray();

    $sent = api()->{$method}();
    expect($sent->send()->getStatusCode())->toBe($status);
})->with([
    ['ok', 200, true],
    ['created', 201, true],
    ['accepted', 202, true],
    ['badRequest', 400, false],
    ['unauthorized', 401, false],
    ['forbidden', 403, false],
    ['notFound', 404, false],
    ['conflict', 409, false],
    ['unprocessable', 422, false],
    ['tooManyRequests', 429, false],
    ['internal', 500, false],
    ['serviceUnavailable', 503, false],
]);

it('carries data through ok()/created()/accepted()', function () {
    expect(api()->ok(['a' => 1])->build()['data'])->toBe(['a' => 1]);
    expect(api()->created(['a' => 1])->build()['data'])->toBe(['a' => 1]);
});

it('attaches errors on badRequest() and unprocessable()', function () {
    $errors = ['email' => ['The email field is required.']];

    expect(api()->badRequest(errors: $errors)->build()['errors'])->toBe($errors);
    expect(api()->unprocessable($errors)->build()['errors'])->toBe($errors);
});

it('uses configured default messages', function () {
    config(['bhry98-api-response.messages.not_found' => 'Nothing here.']);

    expect(api()->notFound()->build()['message'])->toBe('Nothing here.');
});

it('allows overriding the message per call', function () {
    expect(api()->notFound('Custom message.')->build()['message'])->toBe('Custom message.');
});
