<?php

namespace Ssmulders\HashedPassport;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Client;
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
        $this->set_salt();

        $this->register_middleware($router);

        $this->register_observer();

        $this->load_routes();

        if ($this->app->runningInConsole()) {
            $this->register_console_commands_and_migrations();

            $this->publishes([
                __DIR__ . '/../config/hashed-passport.php' => config_path('hashed-passport.php'),
            ], 'config');
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/hashed-passport.php', 'hashed-passport'
        );
    }

    /*
     |--------------------------------------------------------------------------
     | Helpers
     |--------------------------------------------------------------------------
     |
     | To keep things cleaner
     |
     */

    /**
     * Registers the observer that handles the hashed client_id
     */
    private function register_observer()
    {
        $client = Passport::clientModel();
        $client::observe(ClientObserver::class);
    }

    /**
     * Overwrites the Passport routes after the app has loaded to ensure these are used.
     */
    private function load_routes()
    {
        $this->app->booted(function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });
    }

    /**
     * Adds the encryption commands and migrations.
     */
    private function register_console_commands_and_migrations()
    {
        /**
         * Upgrades the secret column's max length from 100 to 2048 characters to support encrypted values.
         * Enables the manual encrypting and decrypting of the client secrets
         */
        if (Passport::$runsMigrations && HashedPassport::$withEncryption) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

            $this->commands([
                Install::class,
                Uninstall::class,
            ]);
        }
    }

    /**
     * Add the Hashids salt with the APP_KEY so it's unique, but constant
     */
    private function set_salt()
    {
        $this->app['config']['hashids.connections.hashed_passport'] = [
            'salt'   => config('hashed-passport.salt'),
            'length' => '32',
        ];
    }

    /**
     * The middleware magic
     *
     * Catches both incoming and outgoing requests and should be compatible with custom routes
     */
    private function register_middleware(Router $router)
    {
        $router->middlewareGroup('hashed_passport', [
            Middleware\DecodeHashedClientIdOnRequest::class,
        ]);
    }
}
