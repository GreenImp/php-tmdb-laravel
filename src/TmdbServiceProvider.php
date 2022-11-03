<?php

namespace Tmdb\Laravel;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tmdb\Laravel\Adapters\EventDispatcherAdapter;
use Tmdb\Laravel\Adapters\EventDispatcherLaravel;

class TmdbServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public const CONFIG_PATH = __DIR__.'/../config/tmdb.php';

    /**
     * All of the container bindings that should be registered.
     *,
     * @var array
     */
    public array $bindings = [
        // Let the IoC container be able to make a Symfony event dispatcher
        EventDispatcherInterface::class => EventDispatcher::class,
        EventDispatcherAdapter::class => EventDispatcherLaravel::class,
    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('tmdb.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(self::CONFIG_PATH, 'tmdb');

        // Setup default configurations for the Tmdb Client
        $this->app->singleton('Tmdb\Client', fn () => app()->make(TmdbApi::class)->getClient());

        // bind the configuration (used by the image helper)
        /*$this->app->bind('Tmdb\Model\Configuration', function() {
            $configuration = $this->app->make('Tmdb\Repository\ConfigurationRepository');
            return $configuration->load();
        });*/
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('tmdb');
    }
}
