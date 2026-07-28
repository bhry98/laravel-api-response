<?php

declare(strict_types=1);

it('renames envelope keys via config', function () {
    config(['bhry98-api-response.keys' => [
        'status' => 'success',
        'message' => 'msg',
        'data' => 'result',
    ]]);

    $result = api()->success()->message('Hi')->data(['a' => 1])->build();

    expect($result)->toMatchArray([
        'success' => true,
        'msg' => 'Hi',
        'result' => ['a' => 1],
    ]);
});

it('wraps the whole envelope in a top-level data key when enabled', function () {
    config(['bhry98-api-response.wrap' => true]);

    $result = api()->success()->message('Hi')->build();

    expect($result)->toHaveKey('data');
    expect($result['data'])->toMatchArray(['status' => true, 'message' => 'Hi']);
});

it('merges extra top-level keys into the envelope', function () {
    $result = api()->success()->extra(['request_id' => 'abc'])->build();

    expect($result['request_id'])->toBe('abc');
});
