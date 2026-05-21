<?php

namespace SohrabAzinfar\OTP;

use Illuminate\Support\ServiceProvider;
use SohrabAzinfar\OTP\Commands\CleanExpiredOtps;
use SohrabAzinfar\OTP\Managers\OtpManager;

class OtpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(OtpManager::class);
        $this->app->alias(OtpManager::class, 'otp');

        $this->mergeConfigFrom(
            __DIR__.'/../config/otp.php',
            'otp'
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/otp.php' => config_path('otp.php'),
        ], 'otp-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanExpiredOtps::class,
            ]);
        }
    }
}