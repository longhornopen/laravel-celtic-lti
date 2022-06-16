<?php

namespace LonghornOpen\LaravelCelticLTI;

use ceLTIc\LTI\Tool;
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

        $this->publishes([
            __DIR__.'/../config/lti.php' => config_path('lti.php'),
        ]);

        try {
            Tool::$defaultTool = LtiTool::getLtiTool();
        } catch (\PDOException $e) {
            // LtiTool tries to connect to the DB.  Can't do that?
            // Not worth stopping boot() for...any real DB connection
            // problems will get exposed later in app code.
            //
            // This catch block might be a sign that this is better implemented as
            // Middleware than as a Service Provider.
        }
    }

    public function register() : void
    {

    }
}