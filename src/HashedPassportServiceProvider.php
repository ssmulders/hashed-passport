<?php

namespace Ssmulders\HashedPassport;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

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
            'salt'   => env('APP_KEY'),
            'length' => '32',
        ];

        /**
         * The middleware magic
         *
         * Catches both incoming and outgoing requests and should be compatible with custom routes
         */
        $router->middlewareGroup('hashed_passport', [
            \Ssmulders\HashedPassport\Middleware\UnHashClientIdOnRequest::class,
            \Ssmulders\HashedPassport\Middleware\HashClientIdOnResponse::class
        ]);

//        $router->aliasMiddleware('hashpass_req', \Ssmulders\HashedPassport\Middleware\UnHashClientIdOnRequest::class);
//        $router->aliasMiddleware('hashpass_resp', \Ssmulders\HashedPassport\Middleware\HashClientIdOnResponse::class);

        /**
         * Overwrites the Passport routes after the app has loaded to ensure these are used.
         */
        $this->app->booted(function () {
            # include routing
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
            $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        });
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
}