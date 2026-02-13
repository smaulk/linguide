<?php
declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Interfaces\Telegram\Middlewares\ResolveTelegramUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use SergiX44\Nutgram\Nutgram;

class TelegramServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->resolveTelegramBot();
    }

    public function boot(): void
    {
        //
    }

    protected function resolveTelegramBot(): void
    {
        $routesPath = app_path('Interfaces/Telegram/Routes/telegram.php');
        if (!file_exists($routesPath)) {
            Log::warning('Telegram routes file does not exist: ' . $routesPath);
            return;
        }

        // Настраиваем бота, когда Nutgram инициализируется
        $this->app->resolving(Nutgram::class, function (Nutgram $bot) use ($routesPath) {
            $bot->middlewares($this->middlewares());

            require $routesPath;
        });
    }

    protected function middlewares(): array
    {
        return [
            ResolveTelegramUser::class,
        ];
    }
}