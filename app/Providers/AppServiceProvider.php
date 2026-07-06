<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Task;
use App\Models\WeddingDetail;
use App\Observers\TaskObserver;
use App\Observers\WeddingDetailObserver;

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
    WeddingDetail::observe(WeddingDetailObserver::class);
    Task::observe(TaskObserver::class);
}
}
