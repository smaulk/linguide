<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Middlewares;

use App\Interfaces\Telegram\Classes\AppUserContext;
use App\Interfaces\Telegram\Parents\TelegramMiddleware;
use App\Interfaces\Telegram\Services\ResolveUserService;
use SergiX44\Nutgram\Nutgram;

final class ResolveUserTgMiddleware extends TelegramMiddleware
{
    public function __construct(
        private readonly ResolveUserService $resolveUserService,
        private readonly AppUserContext $userContext,
    ){}

    public function __invoke(Nutgram $bot, callable $next): void
    {
        $tgUser = $bot->user();
        if ($tgUser === null) {
            return;
        }

        $userDto = $this->resolveUserService->run($tgUser);
        if ($userDto === null) {
            $bot->sendMessage("Произошла ошибка при получении пользователя, попробуйте позже.");
            return;
        }

        $this->userContext->set($bot, $userDto);
        $next($bot);
    }
}