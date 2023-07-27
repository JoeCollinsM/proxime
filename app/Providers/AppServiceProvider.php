<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(config('database.connections.mysql.string_length', 191));

        Blade::directive('able', function ($expression) {
            $caps = explode(', ', $expression);
            $value = count($caps) > 1 ? $caps : $caps[0];
            return "<?php if (\Auth::guard('staff')->user()->hasCap($value)): ?>";
        });

        Blade::directive('endable', function ($expression) {
            return "<?php endif; ?>";
        });

        Validator::extend('slug', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value);
        }, 'The :attribute must be a valid slug');
    }
}
