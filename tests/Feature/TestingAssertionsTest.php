<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::get('/ping', fn () => api()->ok(['id' => 1], 'Pong.'));
    Route::get('/ping-fail', fn () => api()->badRequest('Nope.'));
    Route::get('/ping-validate', fn () => api()->unprocessable(['email' => ['Required.']]));
});

it('asserts a successful api response', function () {
    $this->getJson('/ping')
        ->assertApiSuccess('Pong.')
        ->assertApiStatus(true)
        ->assertApiMessage('Pong.')
        ->assertApiData(['id' => 1]);
});

it('asserts a failing api response', function () {
    $this->getJson('/ping-fail')
        ->assertApiError('Nope.')
        ->assertApiStatus(false);
});

it('asserts validation errors', function () {
    $this->getJson('/ping-validate')
        ->assertApiValidation(['email']);
});
