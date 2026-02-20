<?php
declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Interfaces\Telegram\Middlewares\ResolveTelegramUser;
use App\Interfaces\Telegram\Parents\TelegramMiddleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use SergiX44\Nutgram\Conversations\Conversation;
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
        if (!is_file($routesPath)) {
            Log::warning('Telegram routes file does not exist: ' . $routesPath);
            return;
        }

        // Настраиваем бота, когда Nutgram инициализируется
        $this->app->resolving(Nutgram::class, function (Nutgram $bot) use ($routesPath) {
            /** @phpstan-ignore-next-line */
            $bot->middlewares($this->middlewares());
            // Включение внедрения зависимостей при десериализации диалога
            Conversation::refreshOnDeserialize();

            require $routesPath;
        });
    }

    /**
     * @return list<class-string<TelegramMiddleware>>
     */
    protected function middlewares(): array
    {
        return [
            ResolveTelegramUser::class,
        ];
    }
}