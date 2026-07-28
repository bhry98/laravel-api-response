<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Support;

use Bhry98\LaravelApiResponse\Contracts\DataResolverContract;
use Illuminate\Contracts\Pagination\CursorPaginator as CursorPaginatorContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Normalizes anything a controller might hand to api()->from()/data() -
 * Eloquent models, API resources, resource collections, paginators, plain
 * collections, arrays or DTOs - into data/meta/links.
 */
class DataResolver implements DataResolverContract
{
    public function supports(mixed $data): bool
    {
        return true;
    }

    /**
     * @return array{data: mixed, meta: array<string, mixed>, links: array<string, mixed>}
     */
    public function resolve(mixed $data): array
    {
        return match (true) {
            $data instanceof CursorPaginatorContract => $this->fromCursorPaginator($data),
            $data instanceof LengthAwarePaginatorContract => $this->fromLengthAwarePaginator($data),
            $data instanceof PaginatorContract => $this->fromSimplePaginator($data),
            $data instanceof JsonResource => $this->fromJsonResource($data),
            $data instanceof Arrayable => [
                'data' => $data->toArray(),
                'meta' => [],
                'links' => [],
            ],
            default => ['data' => $data, 'meta' => [], 'links' => []],
        };
    }

    /**
     * Reuse Laravel's own Responsable pipeline so pagination meta/links stay
     * perfectly consistent with what `return $resource;` would produce.
     */
    private function fromJsonResource(JsonResource $resource): array
    {
        $resolved = $resource->response()->getData(true);

        if (! is_array($resolved) || ! array_key_exists('data', $resolved)) {
            return ['data' => $resolved, 'meta' => [], 'links' => []];
        }

        return [
            'data' => $resolved['data'],
            'meta' => $resolved['meta'] ?? [],
            'links' => $resolved['links'] ?? [],
        ];
    }

    private function fromLengthAwarePaginator(LengthAwarePaginatorContract $paginator): array
    {
        return [
            'data' => $this->items($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];
    }

    private function fromSimplePaginator(PaginatorContract $paginator): array
    {
        return [
            'data' => $this->items($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
            ],
            'links' => [
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];
    }

    private function fromCursorPaginator(CursorPaginatorContract $paginator): array
    {
        return [
            'data' => $this->items($paginator->items()),
            'meta' => [
                'per_page' => $paginator->perPage(),
            ],
            'links' => [
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];
    }

    private function items(mixed $items): array
    {
        return array_map(
            fn (mixed $item) => $item instanceof Arrayable ? $item->toArray() : $item,
            is_array($items) ? $items : (array) $items,
        );
    }
}
