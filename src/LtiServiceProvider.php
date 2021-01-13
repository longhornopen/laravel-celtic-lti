<?php

namespace LonghornOpen\LaravelCelticLTI;

use App\Console\Commands\AddLti1p2Platform;
use Illuminate\Support\ServiceProvider;

class LtiServiceProvider extends ServiceProvider
{
    public function boot() : void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                AddLti1p2Platform::class,
            ]);
        }
    }

    public function register() : void
    {

    }
}