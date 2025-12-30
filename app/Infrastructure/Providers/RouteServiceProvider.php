<?php
declare(strict_types=1);

namespace App\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadApiRoutes();
    }

    private function loadApiRoutes(): void
    {
        $paths = glob(app_path('Interfaces/*/Routes/api.*'));
        foreach ($paths as $route_path) {
            Route::middleware('api')
                ->prefix('/api')
                ->group($route_path);
        }
    }
}