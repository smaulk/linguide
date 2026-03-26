<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Core\Modules\User\Actions\UpdateUserSettingAction;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Dto\UserSettingDto;
use App\Core\Modules\User\Enums\LanguageLevel;
use App\Interfaces\Telegram\Commands\BaseCommand;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;
use Throwable;

final class SetUserLevelHandler extends Handler
{
    public function __construct(private readonly UpdateUserSettingAction $updateAction){}

    /**
     * @throws Throwable
     */
    public function __invoke(Nutgram $bot, int $level): void
    {
        $bot->answerCallbackQuery();

        $languageLevel = LanguageLevel::from($level);
        $appUser = $this->getAppUser($bot);

        $this->updateUserLevel($appUser, $languageLevel);

        $bot->editMessageText($this->getText($languageLevel));
    }

    /**
     * @throws Throwable
     */
    private function updateUserLevel(UserDto $appUser, LanguageLevel $level): void
    {
        $this->updateAction->run($appUser->id, new UserSettingDto(
            level: $level,
            utcOffset: $appUser->settings->utcOffset,
            wordsRepeatLimit: $appUser->settings->wordsRepeatLimit,
        ));
    }

    private function getText(LanguageLevel $level): string
    {
        return "Вы установили для себя уровень {$level->name}.";
    }
}