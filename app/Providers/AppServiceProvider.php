<?php

namespace App\Providers;

use App\Services\VisionAPIService;
use App\Services\VisionAPIServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public $bindings = [
        VisionAPIServiceInterface::class => VisionAPIService::class,
    ];

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
        //
    }
}
