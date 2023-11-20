<?php

namespace As247\Puller;

use Illuminate\Contracts\Foundation\CachesRoutes;
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
        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/puller.php', 'puller');
        }
        $this->registerManager();
    }
    function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'puller-migrations');

            $this->publishes([
                __DIR__.'/../config/puller.php' => config_path('puller.php'),
            ], 'puller-config');

        }

    }

    protected function registerRoutes(){
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            return;
        }
        $attributes=[
            'middleware' => $this->app['config']['puller.route.middleware'] ?? ['puller'],
        ];
        $path=$this->app['config']['puller.route.path'] ?: '/puller/messages';

        $this->app['router']->group($attributes, function ($router) use($path) {
            $router->match(['get','post'],$path,
                [PullerController::class, 'messages'])
                ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
                ->name('puller.messages');
        });
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
