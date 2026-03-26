<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Middlewares;

use App\Core\Modules\User\Actions\CheckUserOnboardingAction;
use App\Core\Modules\User\Enums\UserOnboardingStatus;
use App\Interfaces\Telegram\Handlers\SelectionUserLevelHandler;
use App\Interfaces\Telegram\Handlers\SelectionUserTimezoneHandler;
use App\Interfaces\Telegram\Parents\TelegramMiddleware;
use SergiX44\Nutgram\Nutgram;

final class OnboardingUserTgMiddleware extends TelegramMiddleware
{
    public function __construct(
        private readonly CheckUserOnboardingAction $checkOnboardingAction,
        private readonly SelectionUserLevelHandler $levelSelectionHandler,
        private readonly SelectionUserTimezoneHandler $timezoneSelectionHandler,
    ){}

    public function __invoke(Nutgram $bot, callable $next): void
    {
        $appUser = $this->getAppUser($bot);
        $onboardingStatus = $this->checkOnboardingAction->run($appUser->settings);

        match ($onboardingStatus) {
            UserOnboardingStatus::SELECT_LEVEL    => $this->startSelectLevel($bot),
            UserOnboardingStatus::SELECT_TIMEZONE => $this->startSelectTimezone($bot),
            default                               => $next($bot),
        };
    }

    private function startSelectLevel(Nutgram $bot): void
    {
        ($this->levelSelectionHandler)($bot);
    }

    private function startSelectTimezone(Nutgram $bot): void
    {
        ($this->timezoneSelectionHandler)($bot);
    }
}