<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\Cookie;

it('builds a fluent success response', function () {
    $response = api()
        ->success()
        ->message('User created.')
        ->data(['id' => 1])
        ->meta(['foo' => 'bar'])
        ->send();

    expect($response->getStatusCode())->toBe(200);

    $json = $response->getData(true);

    expect($json)->toMatchArray([
        'status' => true,
        'message' => 'User created.',
        'data' => ['id' => 1],
        'meta' => ['foo' => 'bar'],
    ]);
});

it('supports headers and cookies', function () {
    $response = api()
        ->success()
        ->headers(['X-Foo' => 'bar'])
        ->cookie(Cookie::create('token')->withValue('abc'))
        ->send();

    expect($response->headers->get('X-Foo'))->toBe('bar');
    expect($response->headers->getCookies())->toHaveCount(1);
});

it('is returnable directly from a route via Responsable', function () {
    Route::get('/responsable', fn () => api()->created(['id' => 5]));

    $this->get('/responsable')
        ->assertStatus(201)
        ->assertJsonPath('data.id', 5);
});

it('never shares state between two builder instances resolved from the container', function () {
    $first = api()->message('first');
    $second = api()->message('second');

    expect($first->build()['message'])->toBe('first');
    expect($second->build()['message'])->toBe('second');
});

it('omits the body for 204 responses', function () {
    $response = api()->noContent()->send();

    expect($response->getStatusCode())->toBe(204);
    expect($response->getContent())->toBe('');
});
