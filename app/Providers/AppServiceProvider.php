<?php

namespace App\Providers;

use App\Contracts\Repositories\EnrollmentRepositoryInterface;
use App\Contracts\Repositories\WorkshopRepositoryInterface;
use App\Repositories\EnrollmentRepository;
use App\Repositories\WorkshopRepository;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(WorkshopRepositoryInterface::class, WorkshopRepository::class);
        $this->app->bind(EnrollmentRepositoryInterface::class, EnrollmentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
