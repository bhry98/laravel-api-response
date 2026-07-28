<?php

declare(strict_types=1);

use Bhry98\LaravelApiResponse\Data\ResponsePayload;

it('merges meta and links without losing existing entries', function () {
    $payload = new ResponsePayload(meta: ['a' => 1], links: ['self' => '/x']);

    $payload->withMeta(['b' => 2])->withLinks(['next' => '/y']);

    expect($payload->meta)->toBe(['a' => 1, 'b' => 2]);
    expect($payload->links)->toBe(['self' => '/x', 'next' => '/y']);
});

it('lets new meta values win on key collisions', function () {
    $payload = new ResponsePayload(meta: ['a' => 1]);

    $payload->withMeta(['a' => 2]);

    expect($payload->meta)->toBe(['a' => 2]);
});
