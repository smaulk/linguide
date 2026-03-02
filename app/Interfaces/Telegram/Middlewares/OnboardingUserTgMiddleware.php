<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Middlewares;

use App\Core\Modules\User\Actions\CheckUserOnboardingAction;
use App\Interfaces\Telegram\Handlers\ShowLevelSelectionHandler;
use App\Interfaces\Telegram\Parents\TelegramMiddleware;
use SergiX44\Nutgram\Nutgram;

final class OnboardingUserTgMiddleware extends TelegramMiddleware
{
    public function __construct(
        private readonly CheckUserOnboardingAction $checkOnboardingAction,
        private readonly ShowLevelSelectionHandler $levelSelectionHandler,
    ){}

    public function __invoke(Nutgram $bot, callable $next): void
    {
        $appUser = $this->getAppUser($bot);

        $isCompleteOnboarding = $this->checkOnboardingAction->run($appUser);
        if (!$isCompleteOnboarding) {
            $this->startOnboarding($bot);
            return;
        }

        $next($bot);
    }

    private function startOnboarding(Nutgram $bot): void
    {
        ($this->levelSelectionHandler)($bot);
    }
}