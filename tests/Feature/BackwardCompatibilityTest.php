<?php

declare(strict_types=1);

use Bhry98\LaravelApiResponse\Facades\BResponseError;
use Bhry98\LaravelApiResponse\Facades\BResponseNotFound;
use Bhry98\LaravelApiResponse\Facades\BResponseSuccess;
use Bhry98\LaravelApiResponse\Facades\BResponseValidationError;
use Bhry98\LaravelApiResponse\Helpers\ResponseKeys;
use Illuminate\Support\Facades\Validator;

it('keeps the legacy success facade working', function () {
    $response = BResponseSuccess::make()
        ->message('Everything is fine!')
        ->data(['id' => 1])
        ->additions(['request_id' => 'abc'])
        ->toJson();

    expect($response->getStatusCode())->toBe(200);

    $json = $response->getData(true);

    expect($json['status'])->toBeTrue();
    expect($json['message'])->toBe('Everything is fine!');
    expect($json['data'])->toBe(['id' => 1]);
    expect($json['request_id'])->toBe('abc');
});

it('keeps the legacy error facade working and send() matches toJson()', function () {
    $viaToJson = BResponseError::make()->message('Oops.')->toJson()->getData(true);
    $viaSend = BResponseError::make()->message('Oops.')->send()->getData(true);

    expect($viaToJson)->toBe($viaSend);
    expect($viaToJson['status'])->toBeFalse();
});

it('keeps the legacy validation facade working', function () {
    $validator = Validator::make([], ['email' => 'required']);

    $response = BResponseValidationError::make()->fromValidator($validator)->toJson();

    expect($response->getStatusCode())->toBe(422);
    expect($response->getData(true)['errors'])->toHaveKey('email');
});

it('keeps the legacy not found facade working', function () {
    expect(BResponseNotFound::make()->toJson()->getStatusCode())->toBe(404);
});

it('still exposes the ResponseKeys enum', function () {
    expect(ResponseKeys::Status->value)->toBe('status');
    expect(ResponseKeys::Message->value)->toBe('message');
});

it('respects configured keys and wrap for legacy responses too', function () {
    config(['bhry98-api-response.keys' => ['status' => 'success', 'message' => 'msg']]);

    $json = BResponseSuccess::make()->message('Hi')->toJson()->getData(true);

    expect($json)->toHaveKeys(['success', 'msg']);
});
