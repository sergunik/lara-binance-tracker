<?php

namespace App\Providers;

use App\Services\ChatGPTService;
use App\Services\Interfaces\ChatGPTServiceInterface;
use App\Services\BinanceService;
use App\Services\Interfaces\BinanceServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ChatGPTServiceInterface::class, ChatGPTService::class);
        $this->app->bind(BinanceServiceInterface::class, BinanceService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
