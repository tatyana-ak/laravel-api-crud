<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Dingo\Api\Routing\Router as ApiRouter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * This version is applied to the API routes in your routes file.
     *
     * Check dingo/api's documentation for more info.
     *
     * @var string
     */
    protected $version = 'v1';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     * @param ApiRouter $apiRouter
     * @return void
     */
    public function map(ApiRouter $apiRouter)
    {
        $this->mapApiRoutes($apiRouter);

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     * @param ApiRouter $apiRouter
     * @return void
     */
    protected function mapApiRoutes(ApiRouter $apiRouter)
    {
        $apiRouter->version($this->version, function ($apiRouter) {
            $apiRouter->group([
                'prefix' => 'v1',
                'namespace' => $this->namespace,
                'middleware' => ['api', 'force.ssl'],
            ], function ($apiRouter) {
                require base_path('routes/api.php');
            });
        });
    }
}
