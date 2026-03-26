<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Core\Modules\User\Actions\UpdateUserSettingAction;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Dto\UserSettingDto;
use App\Core\Modules\User\Vo\UtcOffset;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;
use Throwable;

final class SetUserTimezoneHandler extends Handler
{
    public function __construct(private readonly UpdateUserSettingAction $updateAction){}

    /**
     * @throws Throwable
     */
    public function __invoke(Nutgram $bot, int $offset): void
    {
        $bot->answerCallbackQuery();

        $utcOffset = UtcOffset::fromInt($offset);
        $appUser = $this->getAppUser($bot);

        $this->updateUserTimezone($appUser, $utcOffset);

        $bot->editMessageText($this->getText($utcOffset));
    }

    /**
     * @throws Throwable
     */
    private function updateUserTimezone(UserDto $appUser, UtcOffset $utcOffset): void
    {
        $this->updateAction->run($appUser->id, new UserSettingDto(
            level: $appUser->settings->level,
            utcOffset: $utcOffset,
            wordsRepeatLimit: $appUser->settings->wordsRepeatLimit,
        ));
    }

    private function getText(UtcOffset $utcOffset): string
    {
        return "Вы установили для себя часовой пояс: $utcOffset";
    }
}