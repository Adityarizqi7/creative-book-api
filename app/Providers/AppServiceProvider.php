<?php

namespace App\Providers;

use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale(('id'));
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
