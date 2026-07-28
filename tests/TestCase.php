<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Tests;

use Bhry98\LaravelApiResponse\Facades\ApiResponse;
use Bhry98\LaravelApiResponse\Providers\BResponseServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            BResponseServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'ApiResponse' => ApiResponse::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.debug', false);

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('queue.default', 'sync');
    }
}
