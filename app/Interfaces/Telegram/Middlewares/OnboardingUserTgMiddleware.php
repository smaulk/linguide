<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Middlewares;

use App\Core\Modules\User\Actions\ResolveOnboardingStepAction;
use App\Core\Modules\User\Enums\UserOnboardingStep;
use App\Interfaces\Telegram\Classes\AppUserContext;
use App\Interfaces\Telegram\Parents\TelegramMiddleware;
use App\Interfaces\Telegram\Services\ProcessOnboardingService;
use SergiX44\Nutgram\Nutgram;

final class OnboardingUserTgMiddleware extends TelegramMiddleware
{
    public function __construct(
        private readonly ResolveOnboardingStepAction $resolveStepAction,
        private readonly ProcessOnboardingService $processOnboardingService,
        private readonly AppUserContext $userContext,
    ){}

    public function __invoke(Nutgram $bot, callable $next): void
    {
        $appUser = $this->userContext->get($bot);
        $step = $this->resolveStepAction->run($appUser->settings);

        if ($step !== UserOnboardingStep::COMPLETED) {
            $this->processOnboardingService->run($bot, $appUser);
            return;
        }

        $next($bot);
    }
}