<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\LogFake;

class LogFakeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('log.fake', function ($app) {
            return new LogFake();
        });
    }

    public function boot(): void
    {
        //
    }
}