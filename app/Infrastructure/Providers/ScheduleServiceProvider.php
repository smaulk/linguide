<?php
declare(strict_types=1);

namespace App\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ScheduleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(Schedule $schedule): void
    {
        require app_path('Interfaces/Console/Schedule/schedule.php');
    }
}