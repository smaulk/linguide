<?php
declare(strict_types=1);

namespace App\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $paths = glob(app_path('Core/Modules/*/Data/Migrations/*'));
        $this->loadMigrationsFrom($paths);
    }
}