<?php

namespace As247\Puller;

use Illuminate\Support\ServiceProvider;
use As247\Puller\Connectors\DatabaseConnector;
use As247\Puller\Connectors\RedisConnector;

class PullerServiceProvider extends ServiceProvider
{
    function register()
    {
        $this->app->singleton('puller', function ($app) {
            return new PullerManager($app);
        });
        if (! app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/puller.php', 'puller');
        }
    }
    function boot()
    {

        if (app()->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'puller-migrations');

            $this->publishes([
                __DIR__.'/../config/sanctum.php' => config_path('sanctum.php'),
            ], 'puller-config');

        }
    }
    protected function registerManager(){
        $this->app->singleton('puller', function ($app) {
            $manager=new PullerManager($app);
            $this->registerConnectors($manager);
            return $manager;
        });
    }
    protected function registerConnection(){
        $this->app->singleton('puller.connection', function ($app) {
            $manager=$app['puller'];
            return $manager->connection();
        });
    }

    /**
     * @param $manager
     * @return void
     */
    protected function registerConnectors($manager){
        foreach (['Database', 'Redis'] as $connector) {
            $this->{"register{$connector}Connector"}($manager);
        }
    }
    protected function registerDatabaseConnector($manager){
        $manager->addConnector('database', function () {
            return new DatabaseConnector($this->app['db']);
        });
    }
    protected function registerRedisConnector($manager){
        $manager->addConnector('redis', function () {
            return new RedisConnector($this->app['redis']);
        });
    }
}
