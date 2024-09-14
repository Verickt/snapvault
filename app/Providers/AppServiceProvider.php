<?php

namespace App\Providers;

use App\Services\ChatGPTService;
use App\Services\ChatGPTServiceInterface;
use App\Services\ImageService;
use App\Services\ImageServiceInterface;
use App\Services\VisionAPIService;
use App\Services\VisionAPIServiceInterface;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{

    public $bindings = [
        ChatGPTServiceInterface::class => ChatGPTService::class,
        ImageServiceInterface::class => ImageService::class,
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
        Inertia::share([
            'flash' => function () {
                return [
                    'success' => session('success'),
                    'error' => session('error'),
                    // Add other flash message types if needed
                ];
            },
        ]);
    }
}
