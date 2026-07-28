<?php

declare(strict_types=1);

use Bhry98\LaravelApiResponse\Builder\ResponseBuilder;
use Bhry98\LaravelApiResponse\Facades\ApiResponse;

it('supports registering a custom reusable response as a macro', function () {
    ResponseBuilder::macro('invoicePaid', function (array $invoice) {
        /** @var ResponseBuilder $this */
        return $this->success()->message('Invoice paid.')->data($invoice);
    });

    $result = api()->invoicePaid(['id' => 12])->build();

    expect($result['message'])->toBe('Invoice paid.');
    expect($result['data'])->toBe(['id' => 12]);
});

it('supports registering macros through the facade', function () {
    ApiResponse::macro('teapot', function () {
        /** @var ResponseBuilder $this */
        return $this->fail()->statusCode(418)->message("I'm a teapot.");
    });

    expect(ApiResponse::teapot()->send()->getStatusCode())->toBe(418);
});
