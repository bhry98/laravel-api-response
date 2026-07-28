<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Tests\Fixtures;

use Illuminate\Contracts\Support\Arrayable;

final class InvoiceDto implements Arrayable
{
    public function __construct(
        public readonly int $id,
        public readonly float $total,
    ) {}

    public function toArray(): array
    {
        return ['id' => $this->id, 'total' => $this->total];
    }
}
