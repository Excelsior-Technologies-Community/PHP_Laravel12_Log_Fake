<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ExtendedLogFake;

class LogFakeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('log.fake', function ($app) {
            return new ExtendedLogFake();
        });
    }
    
    public function boot()
    {
        //
    }
}