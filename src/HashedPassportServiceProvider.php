<?php

namespace Ssmulders\HashedPassport;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Ssmulders\HashedPassport\Commands\Install;
use Ssmulders\HashedPassport\Commands\Uninstall;
use Ssmulders\HashedPassport\Observers\ClientObserver;

class HashedPassportServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap
     *
     * @param Router $router
     */
    public function boot(Router $router)
    {
        /**
         * Add the Hashids salt with the APP_KEY so it's unique, but constant
         */
        $this->app['config']['hashids.connections.client_id'] = [
            'salt'   => config('hashed_passport.client_id_salt', env('APP_NAME')),
            'length' => '32',
        ];

        $this->publishes([
            __DIR__ . '/config/hashed-passport.php' => config_path('hashed-passport.php')
        ], 'config');

        $this->load_middleware($router);
        $this->load_routes();

        $this->runs_in_console();

        \Laravel\Passport\Client::observe(ClientObserver::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }


    /*
     |--------------------------------------------------------------------------
     | Helpers
     |--------------------------------------------------------------------------
     |
     | To keep things clean
     |
     |
     */
    private function runs_in_console()
    {
        /**
         * Adds the encryption commands.
         */
        if ($this->app->runningInConsole())
        {
            /**
             * Upgrades the secret column's max length from 100 to 2048 characters to support encrypted values.
             */
            if (HashedPassport::$withEncryption)
            {
                $this->loadMigrationsFrom(__DIR__ . '/migrations');
            }

            $this->commands([
                Install::class,
                Uninstall::class,
            ]);
        }
    }

    /**
     * Overwrites the Passport routes after the app has loaded to ensure these are used.
     */
    private function load_routes()
    {
        $this->app->booted(function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        });
    }

    /**
     * The middleware magic
     *
     * Catches both incoming and outgoing requests and should be compatible with custom routes
     *
     * @param Router $router
     */
    private function load_middleware(Router $router)
    {
        $router->middlewareGroup('hashed_passport', [
            \Ssmulders\HashedPassport\Middleware\DecodeHashedClientIdOnRequest::class,
        ]);
    }
}