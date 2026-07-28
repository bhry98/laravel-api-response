<?php

declare(strict_types=1);

use Bhry98\LaravelApiResponse\Tests\Fixtures\InvoiceDto;
use Bhry98\LaravelApiResponse\Tests\Fixtures\UserResource;
use Bhry98\LaravelApiResponse\Tests\Fixtures\UserStub;
use Illuminate\Support\Collection;

it('passes plain arrays through untouched', function () {
    expect(api()->from(['a' => 1])->build()['data'])->toBe(['a' => 1]);
});

it('serializes an Eloquent model via from()', function () {
    $model = new UserStub(['id' => 1, 'name' => 'Alice']);

    expect(api()->from($model)->build()['data'])->toBe(['id' => 1, 'name' => 'Alice']);
});

it('serializes a plain collection', function () {
    $collection = new Collection([['id' => 1], ['id' => 2]]);

    expect(api()->from($collection)->build()['data'])->toBe([['id' => 1], ['id' => 2]]);
});

it('serializes an Arrayable DTO', function () {
    $dto = new InvoiceDto(1, 99.5);

    expect(api()->from($dto)->build()['data'])->toBe(['id' => 1, 'total' => 99.5]);
});

it('resolves a single API resource', function () {
    $model = new UserStub(['id' => 1, 'name' => 'Alice']);

    $data = api()->resource(UserResource::make($model))->build()['data'];

    expect($data)->toBe(['id' => 1, 'name' => 'Alice']);
});

it('resolves a resource collection', function () {
    $models = collect([
        new UserStub(['id' => 1, 'name' => 'Alice']),
        new UserStub(['id' => 2, 'name' => 'Bob']),
    ]);

    $data = api()->resourceCollection(UserResource::collection($models))->build()['data'];

    expect($data)->toBe([
        ['id' => 1, 'name' => 'Alice'],
        ['id' => 2, 'name' => 'Bob'],
    ]);
});
