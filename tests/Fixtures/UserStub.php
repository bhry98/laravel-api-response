<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class UserStub extends Model
{
    protected $guarded = [];

    public $timestamps = false;
}
