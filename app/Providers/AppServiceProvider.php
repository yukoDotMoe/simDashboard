<?php

namespace App\Providers;

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
        $this->app->bind(
            'App\Repositories\SimsRepositoryInterface',
            'App\Repositories\SimsRepository'
        );

        $this->app->bind(
            'App\Repositories\ServiceRepositoryInterface',
            'App\Repositories\ServiceRepository'
        );

        $this->app->bind(
            'App\Repositories\NetworkRepositoryInterface',
            'App\Repositories\NetworkRepository'
        );

        $this->app->bind(
            'App\Repositories\BalanceRepositoryInterface',
            'App\Repositories\BalanceRepository'
        );

        $this->app->bind(
            'App\Repositories\ActivityRepositoryInterface',
            'App\Repositories\ActivityRepository'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if($this->app->environment('production') || $this->app->environment('staging')) {
            \URL::forceScheme('https');
        }
    }
}
