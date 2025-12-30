<?php
declare(strict_types=1);

namespace App\Infrastructure\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use SergiX44\Nutgram\Nutgram;

class TelegramServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadTelegramRoutes();
    }

    public function boot(): void
    {
        //
    }

    protected function loadTelegramRoutes(): void
    {
        $routesPath = app_path('Interfaces/Telegram/Routes/telegram.php');
        if (!file_exists($routesPath)) {
            Log::warning('Telegram routes file does not exist: ' . $routesPath);
            return;
        }

        // Настраиваем бота, когда Nutgram инициализируется
        $this->app->resolving(Nutgram::class, function (Nutgram $bot) use ($routesPath) {
            require $routesPath;
        });
    }
}