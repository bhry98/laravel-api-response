<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

it('leaves exceptions alone when the override is disabled', function () {
    config(['bhry98-api-response.exceptions.override_exception_handler' => false]);

    Route::get('/boom-disabled', function () {
        throw ValidationException::withMessages(['email' => ['Required.']]);
    });

    $response = $this->getJson('/boom-disabled');

    expect($response->json())->not->toHaveKey('status');
});

it('formats a ValidationException into an api() response when enabled', function () {
    config(['bhry98-api-response.exceptions.override_exception_handler' => true]);

    Route::get('/boom-validation', function () {
        throw ValidationException::withMessages(['email' => ['Required.']]);
    });

    $this->getJson('/boom-validation')
        ->assertStatus(422)
        ->assertJsonPath('status', false)
        ->assertJsonPath('errors.email.0', 'Required.');
});

it('formats a generic exception into an internal error when enabled', function () {
    config(['bhry98-api-response.exceptions.override_exception_handler' => true]);

    Route::get('/boom-generic', function () {
        throw new RuntimeException('secret internals');
    });

    $this->getJson('/boom-generic')
        ->assertStatus(500)
        ->assertJsonPath('status', false);
});
