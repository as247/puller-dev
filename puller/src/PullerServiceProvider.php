<?php

namespace As247\Puller;

use Illuminate\Support\Facades\Route;
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
        $this->app->alias('puller', PullerManager::class);
        if (! app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/puller.php', 'puller');
        }
        $this->registerManager();
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
        Route::post('/puller/messages', [PullerController::class, 'messages'])->name('puller.messages');
    }
    protected function registerManager(){
        $this->app->singleton('puller', function ($app) {
            $manager=new PullerManager($app);
            $this->registerConnectors($manager);
            return $manager;
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
