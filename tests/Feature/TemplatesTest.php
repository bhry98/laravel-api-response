<?php

declare(strict_types=1);

use Bhry98\LaravelApiResponse\Exceptions\TemplateNotFoundException;

it('builds a response from a configured template', function () {
    config(['bhry98-api-response.responses.user_created' => [
        'status' => true,
        'message' => 'User created successfully.',
        'code' => 201,
    ]]);

    $response = api()->template('user_created')->send();

    expect($response->getStatusCode())->toBe(201);
    expect($response->getData(true)['message'])->toBe('User created successfully.');
});

it('allows overriding template fields at call time', function () {
    config(['bhry98-api-response.responses.invoice_paid' => [
        'status' => true,
        'message' => 'Invoice paid.',
        'code' => 200,
    ]]);

    $response = api()->template('invoice_paid', ['data' => ['id' => 9]])->build();

    expect($response['data'])->toBe(['id' => 9]);
});

it('throws when the template does not exist', function () {
    api()->template('does_not_exist');
})->throws(TemplateNotFoundException::class);
