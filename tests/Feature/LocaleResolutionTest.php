<?php

declare(strict_types=1);

use Bhry98\LaravelApiResponse\Tests\Fixtures\AuthenticatableUserStub;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware('api.locale')->get('/locale-check', fn () => api()->ok(['locale' => app()->getLocale()]));
});

it('does nothing when disabled', function () {
    config(['bhry98-api-response.locale.enabled' => false]);
    app()->setLocale('en');

    $this->getJson('/locale-check', ['Accept-Language' => 'fr'])
        ->assertJsonPath('data.locale', 'en');
});

it('prioritizes the Accept-Language header over everything else', function () {
    config([
        'bhry98-api-response.locale.enabled' => true,
        'bhry98-api-response.locale.supported' => ['en', 'fr', 'ar'],
    ]);

    $user = new AuthenticatableUserStub(['locale' => 'ar']);
    $this->be($user);

    $this->getJson('/locale-check', ['Accept-Language' => 'fr-CA,fr;q=0.9,en;q=0.8'])
        ->assertJsonPath('data.locale', 'fr');
});

it('falls back to the authenticated user locale column when no header matches', function () {
    config([
        'bhry98-api-response.locale.enabled' => true,
        'bhry98-api-response.locale.supported' => ['en', 'ar'],
    ]);

    $user = new AuthenticatableUserStub(['locale' => 'ar']);
    $this->be($user);

    $this->getJson('/locale-check', ['Accept-Language' => ''])->assertJsonPath('data.locale', 'ar');
});

it('supports a custom user locale column name', function () {
    config([
        'bhry98-api-response.locale.enabled' => true,
        'bhry98-api-response.locale.user_column' => 'preferred_language',
        'bhry98-api-response.locale.supported' => [],
    ]);

    $user = new AuthenticatableUserStub(['preferred_language' => 'de']);
    $this->be($user);

    $this->getJson('/locale-check', ['Accept-Language' => ''])->assertJsonPath('data.locale', 'de');
});

it('falls back to the configured fallback locale otherwise', function () {
    config([
        'bhry98-api-response.locale.enabled' => true,
        'bhry98-api-response.locale.fallback' => 'ja',
    ]);

    $this->getJson('/locale-check', ['Accept-Language' => ''])->assertJsonPath('data.locale', 'ja');
});
