<?php

namespace App\Providers;

use App\Channel;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->share('channels', Channel::all());

        /* add variable to individual views
        view()->composer('threads.create', function ($view) {
            $view->with('channels',\App\Channel::all());
        });
        */
    }
}
