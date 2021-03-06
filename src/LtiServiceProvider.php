<?php

namespace LonghornOpen\LaravelCelticLTI;

use Illuminate\Support\ServiceProvider;
use LonghornOpen\LaravelCelticLTI\Commands\AddLti1p2Platform;
use LonghornOpen\LaravelCelticLTI\Commands\AddLti1p3Platform;

class LtiServiceProvider extends ServiceProvider
{
    public function boot() : void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                AddLti1p2Platform::class,
                AddLti1p3Platform::class,
            ]);
        }
    }

    public function register() : void
    {

    }
}