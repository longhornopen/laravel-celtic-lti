<?php

namespace LonghornOpen\LaravelCelticLTI;

use Illuminate\Support\ServiceProvider;

class LtiServiceProvider extends ServiceProvider
{
    public function boot() : void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register() : void
    {

    }
}