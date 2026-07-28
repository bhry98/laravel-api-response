<?php

declare(strict_types=1);

use Bhry98\LaravelApiResponse\Exceptions\PresetNotFoundException;
use Bhry98\LaravelApiResponse\Presets\PresetManager;
use Bhry98\LaravelApiResponse\Tests\Fixtures\UppercasePreset;

it('formats the minimal preset without meta/links/extras', function () {
    $result = api()
        ->preset('minimal')
        ->success()
        ->message('Hi')
        ->data(['a' => 1])
        ->meta(['should' => 'be-dropped'])
        ->build();

    expect($result)->toBe([
        'status' => true,
        'message' => 'Hi',
        'data' => ['a' => 1],
    ]);
});

it('strips null values with the mobile preset', function () {
    $result = api()
        ->preset('mobile')
        ->success()
        ->message('Hi')
        ->data(['a' => 1, 'b' => null])
        ->build();

    expect($result['data'])->toBe(['a' => 1]);
});

it('reshapes into a jsonapi-inspired envelope', function () {
    $result = api()
        ->preset('jsonapi')
        ->success()
        ->message('Hi')
        ->data(['a' => 1])
        ->build();

    expect($result)->toHaveKeys(['data', 'meta']);
    expect($result['meta']['message'])->toBe('Hi');
});

it('allows registering a fully custom preset at runtime', function () {
    app(PresetManager::class)->register('shouty', new UppercasePreset);

    $result = api()->preset('shouty')->success()->message('hi')->build();

    expect($result)->toBe(['STATUS' => true, 'MESSAGE' => 'HI']);
});

it('throws for an unknown preset', function () {
    api()->preset('does-not-exist')->build();
})->throws(PresetNotFoundException::class);
