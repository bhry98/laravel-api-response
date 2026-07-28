<?php

declare(strict_types=1);

use Bhry98\LaravelApiResponse\Tests\Fixtures\UserResource;
use Bhry98\LaravelApiResponse\Tests\Fixtures\UserStub;
use Illuminate\Pagination\LengthAwarePaginator;

it('formats a length-aware paginator with meta and links', function () {
    $paginator = new LengthAwarePaginator(
        items: [['id' => 1], ['id' => 2]],
        total: 72,
        perPage: 2,
        currentPage: 1,
        options: ['path' => '/users'],
    );

    $result = api()->paginate($paginator)->build();

    expect($result['data'])->toBe([['id' => 1], ['id' => 2]]);
    expect($result['meta'])->toMatchArray([
        'current_page' => 1,
        'last_page' => 36,
        'per_page' => 2,
        'total' => 72,
    ]);
    expect($result['links'])->toHaveKeys(['first', 'last', 'prev', 'next']);
});

it('formats a paginated resource collection using the same envelope shape', function () {
    $paginator = new LengthAwarePaginator(
        items: [
            new UserStub(['id' => 1, 'name' => 'Alice']),
        ],
        total: 1,
        perPage: 15,
        currentPage: 1,
        options: ['path' => '/users'],
    );

    $result = api()->paginate(UserResource::collection($paginator))->build();

    expect($result['data'])->toBe([['id' => 1, 'name' => 'Alice']]);
    expect($result['meta']['total'])->toBe(1);
});
