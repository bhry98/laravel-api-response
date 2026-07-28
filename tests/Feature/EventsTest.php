<?php

declare(strict_types=1);

use Bhry98\LaravelApiResponse\Events\ResponseCreated;
use Bhry98\LaravelApiResponse\Events\ResponseFailed;
use Bhry98\LaravelApiResponse\Events\ResponseSent;
use Illuminate\Support\Facades\Event;

it('dispatches ResponseCreated for every response', function () {
    Event::fake([ResponseCreated::class]);

    api()->success()->build();

    Event::assertDispatched(ResponseCreated::class);
});

it('dispatches ResponseFailed only for failing responses', function () {
    Event::fake([ResponseFailed::class]);

    api()->success()->build();
    Event::assertNotDispatched(ResponseFailed::class);

    api()->fail()->build();
    Event::assertDispatched(ResponseFailed::class);
});

it('dispatches ResponseSent when the response is actually sent', function () {
    Event::fake([ResponseSent::class]);

    api()->success()->send();

    Event::assertDispatched(ResponseSent::class);
});
