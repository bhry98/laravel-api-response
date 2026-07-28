<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Authenticatable;

class AuthenticatableUserStub extends Authenticatable
{
    protected $guarded = [];

    public $timestamps = false;
}
