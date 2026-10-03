<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Core\Modules\User\Actions\UpdateUserSettingAction;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Dto\UserSettingsDto;
use App\Interfaces\Telegram\Classes\AppUserContext;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;
use Throwable;

final class SetUserRemindersHandler extends Handler
{
    public function __construct(
        private readonly AppUserContext $userContext,
        private readonly UpdateUserSettingAction $updateAction
    ){}

    /**
     * @throws Throwable
     */
    public function __invoke(Nutgram $bot, int $enabled): void
    {
        $bot->answerCallbackQuery();

        $remindersEnabled = (bool)$enabled;
        $appUser = $this->userContext->get($bot);

        $this->updateUserReminders($appUser, $remindersEnabled);

        $bot->editMessageText($this->getText($remindersEnabled));
    }

    /**
     * @throws Throwable
     */
    private function updateUserReminders(UserDto $appUser, bool $remindersEnabled): void
    {
        $this->updateAction->run($appUser->id, new UserSettingsDto(
            level: $appUser->settings->level,
            utcOffset: $appUser->settings->utcOffset,
            reviewLimit: $appUser->settings->reviewLimit,
            remindersEnabled: $remindersEnabled,
        ));
    }

    private function getText(bool $remindersEnabled): string
    {
        $change = $remindersEnabled ? 'включили' : 'выключили';
        return "Вы {$change} напоминания о повторении!";
    }
}