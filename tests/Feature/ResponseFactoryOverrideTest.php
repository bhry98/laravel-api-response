<?php

declare(strict_types=1);

it('leaves response()->json() untouched by default', function () {
    $data = response()->json(['a' => 1])->getData(true);

    expect($data)->toBe(['a' => 1]);
});

it('wraps response()->json() in the standard envelope when enabled', function () {
    config(['bhry98-api-response.override_default_response' => true]);

    $data = response()->json(['a' => 1])->getData(true);

    expect($data)->toBe(['status' => true, 'data' => ['a' => 1]]);
});
