<?php
declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Core\Modules\Notification\Contracts\NotifierContract;
use App\Infrastructure\Modules\Notification\Notifiers\NotificationManager;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NotifierContract::class, function ($app): NotificationManager {
            return new NotificationManager($app->tagged('notification.channels'));
        });
    }

    public function boot(): void
    {
        //
    }
}
