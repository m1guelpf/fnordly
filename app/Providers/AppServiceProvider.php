<?php

namespace App\Providers;

use Laravel\Horizon\Horizon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

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
