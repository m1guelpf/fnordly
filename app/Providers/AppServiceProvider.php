<?php

namespace App\Providers;

use Laravel\Horizon\Horizon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::unguard();

        Horizon::auth(function (\Illuminate\Http\Request $request) {
            return app()->environment('local') || (! is_null($request->user()) && $request->user()->email == 'soy@miguelpiedrafita.com');
        });

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
