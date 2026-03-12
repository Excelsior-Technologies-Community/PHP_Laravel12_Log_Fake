<?php

namespace Timacdonald\LogFake;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class LogFakeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('log.fake', function ($app) {
            return new LogFake();
        });
    }

    public function boot()
    {
        // Bind fake into logger
        Log::extend('fake', function () {
            return new LogFake;
        });
    }
}