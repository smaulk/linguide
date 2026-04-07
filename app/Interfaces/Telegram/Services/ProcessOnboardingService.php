<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Services;

use App\Core\Modules\User\Actions\FindUserByIdAction;
use App\Core\Modules\User\Actions\ResolveOnboardingStepAction;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Dto\UserSettingsDto;
use App\Core\Modules\User\Enums\UserOnboardingStep;
use App\Interfaces\Telegram\Classes\CallbackParser;
use App\Interfaces\Telegram\Commands\SettingsMenuCommand;
use App\Interfaces\Telegram\Handlers\SelectionUserLevelHandler;
use App\Interfaces\Telegram\Handlers\SelectionUserTimezoneHandler;
use App\Interfaces\Telegram\Handlers\SetUserLevelHandler;
use App\Interfaces\Telegram\Handlers\SetUserTimezoneHandler;
use App\Interfaces\Telegram\Handlers\StartHandler;
use App\Interfaces\Telegram\Parents\Service;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;

final class ProcessOnboardingService extends Service
{
    public function __construct(
        private readonly FindUserByIdAction $findUserAction,
        private readonly ResolveOnboardingStepAction $resolveStepAction,
        private readonly SelectionUserLevelHandler $selectionLevelHandler,
        private readonly SetUserLevelHandler $setLevelHandler,
        private readonly SelectionUserTimezoneHandler $selectionTimezoneHandler,
        private readonly SetUserTimezoneHandler $setTimezoneHandler,
        private readonly StartHandler $startHandler,
    ){}

    public function run(Nutgram $bot, UserDto $appUser): void
    {
        if ($this->checkCallback($bot, $appUser->id)) {
            return;
        }

        $this->resolveStep($bot, $appUser->settings);
    }

    private function checkCallback(Nutgram $bot, int $appUserId): bool
    {
        $callback = $bot->callbackQuery()?->data;
        if ($callback === null) {
            return false;
        }

        foreach ($this->callbackMap() as $prefix => $handler) {
            if ($this->handleCallback($bot, $callback, $prefix, $handler, $appUserId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, callable(Nutgram, int): void>
     */
    private function callbackMap(): array
    {
        return [
            SettingsMenuCommand::SET_LEVEL_CALLBACK->value    => $this->setLevelHandler,
            SettingsMenuCommand::SET_TIMEZONE_CALLBACK->value => $this->setTimezoneHandler,
        ];
    }

    /**
     * @param callable(Nutgram, int):void $handler
     */
    private function handleCallback(Nutgram $bot, string $callback, string $prefix, callable $handler, int $appUserId): bool
    {
        if (!CallbackParser::isMatch($callback, $prefix)) {
            return false;
        }

        $value = CallbackParser::parseIntValue($callback, $prefix);

        if ($value === null) {
            Log::warning('Invalid callback value.', [
                'callback' => $callback,
                'prefix'   => $prefix,
            ]);
            return true;
        }

        $handler($bot, $value);
        $this->next($bot, $appUserId);

        return true;
    }

    private function next(Nutgram $bot, int $appUserId): void
    {
        $freshUser = $this->findUserAction->run($appUserId);
        if ($freshUser === null) {
            return;
        }

        $this->resolveStep($bot, $freshUser->settings);
    }

    private function resolveStep(Nutgram $bot, UserSettingsDto $settings): void
    {
        $step = $this->resolveStepAction->run($settings);

        match ($step) {
            UserOnboardingStep::ASK_LEVEL    => ($this->selectionLevelHandler)($bot),
            UserOnboardingStep::ASK_TIMEZONE => ($this->selectionTimezoneHandler)($bot),
            UserOnboardingStep::COMPLETED    => ($this->startHandler)($bot),
        };
    }
}