<?php

namespace EFrame\Sanitize\Providers;

use EFrame\Sanitize\Registrar;
use EFrame\Sanitize\Sanitizer;
use EFrame\Sanitize\Contracts;
use Laravel\Lumen\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container;

class SanitizeServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Contracts\Registrar::class,
            function (Container $app) {
                return new Registrar($app);
            }
        );

        $this->app->bind(Sanitizer::class,
            function (Application $app) {
                return (new Sanitizer)->setRegistrar(
                    $app->make(Registrar::class)
                );
            }
        );
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [
            Sanitizer::class,
            Registrar::class,
        ];
    }
}