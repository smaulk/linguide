<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Middlewares;

use App\Interfaces\Telegram\Classes\AppUserContext;
use App\Interfaces\Telegram\Parents\TelegramMiddleware;
use App\Interfaces\Telegram\Services\EnsureUserHasAccessService;
use SergiX44\Nutgram\Nutgram;

final class EnsureUserHasAccessTgMiddleware extends TelegramMiddleware
{
    public function __construct(
        private readonly AppUserContext $userContext,
        private readonly EnsureUserHasAccessService $ensureHasAccessService,
    ){}

    public function __invoke(Nutgram $bot, callable $next): void
    {
        $appUser = $this->userContext->get($bot);

        if (!$this->ensureHasAccessService->run($bot, $appUser)) {
            return;
        }

        $next($bot);
    }
}