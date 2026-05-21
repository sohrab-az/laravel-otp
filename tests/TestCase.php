<?php

namespace SohrabAzinfar\OTP\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SohrabAzinfar\OTP\OtpServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            OtpServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {

        $app['config']->set('database.default', 'testing');

        $app['config']->set(
            'database.connections.testing',
            [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]
        );

    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->artisan('migrate', [
            '--database' => 'testing',
        ])->run();
    }
}