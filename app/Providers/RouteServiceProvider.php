<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

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
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

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
     *
     * @return void
     */
    public function map()
    {
        $this->mapDeliveryManApiRoutes();

        $this->mapApiRoutes();

        $this->mapShopRoutes();

        $this->mapStaffRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "staff" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapStaffRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace . '\Staff')
            ->prefix('staff')
            ->as('staff.')
            ->group(base_path('routes/staff.php'));
    }

    /**
     * Define the "shop" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapShopRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace . '\Shop')
            ->prefix('shop')
            ->as('shop.')
            ->group(base_path('routes/shop.php'));
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
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace . '\API')
            ->group(base_path('routes/api.php'));
    }

    /**
     * Define the "delivery-man" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapDeliveryManApiRoutes()
    {
        Route::prefix('api/delivery-man')
            ->middleware('api')
            ->namespace($this->namespace . '\API\DeliveryMan')
            ->group(base_path('routes/delivery-man.php'));
    }
}
