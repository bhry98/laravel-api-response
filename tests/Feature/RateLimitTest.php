<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware('api.rate_limit')->get('/rate-limited', fn () => api()->ok(['hit' => true]));
});

it('does nothing when disabled', function () {
    config(['bhry98-api-response.rate_limit.enabled' => false]);

    $this->getJson('/rate-limited')->assertStatus(200);
    $this->getJson('/rate-limited')->assertStatus(200);
});

it('allows requests under the configured max attempts', function () {
    config([
        'bhry98-api-response.rate_limit.enabled' => true,
        'bhry98-api-response.rate_limit.max_attempts' => 2,
        'bhry98-api-response.rate_limit.decay_seconds' => 60,
        'bhry98-api-response.rate_limit.by' => 'ip',
    ]);

    $this->getJson('/rate-limited')
        ->assertStatus(200)
        ->assertHeader('X-RateLimit-Limit', '2')
        ->assertHeader('X-RateLimit-Remaining', '1');
});

it('returns a standard api() 429 envelope once the limit is hit', function () {
    config([
        'bhry98-api-response.rate_limit.enabled' => true,
        'bhry98-api-response.rate_limit.max_attempts' => 1,
        'bhry98-api-response.rate_limit.decay_seconds' => 60,
        'bhry98-api-response.rate_limit.by' => 'ip',
    ]);

    $this->getJson('/rate-limited')->assertStatus(200);

    $this->getJson('/rate-limited')
        ->assertStatus(429)
        ->assertJsonPath('status', false)
        ->assertHeader('X-RateLimit-Limit', '1')
        ->assertHeader('X-RateLimit-Remaining', '0');
});
