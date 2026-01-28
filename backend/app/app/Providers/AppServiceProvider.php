<?php

namespace App\Providers;

use App\Models\Business;
use App\Models\Product;
use App\Observers\BusinessObserver;
use App\Policies\ProductPolicy;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

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
        Passport::tokensCan([
            'user' => 'User Type',
        ]);
        Passport::setDefaultScope([
            'user'
        ]);

    }

    protected $policies = [
        Product::class => ProductPolicy::class,
    ];
}
