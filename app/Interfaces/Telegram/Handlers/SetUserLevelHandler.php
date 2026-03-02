<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Core\Modules\User\Actions\UpdateUserAction;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Enums\LanguageLevel;
use App\Interfaces\Telegram\Commands\BaseCommand;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;
use Throwable;

final class SetUserLevelHandler extends Handler
{
    public function __construct(private readonly UpdateUserAction $updateAction){}

    /**
     * @throws Throwable
     */
    public function __invoke(Nutgram $bot, string $level): void
    {
        $bot->answerCallbackQuery();

        $level = LanguageLevel::from($level);
        $appUser = $this->getAppUser($bot);

        if($appUser->level !== null){
            $bot->editMessageText("У вас уже установлен уровень {$appUser->level->value}!");
            return;
        }

        $this->updateUserLevel($appUser, $level);
        $bot->editMessageText($this->getText($level),);
    }

    /**
     * @throws Throwable
     */
    private function updateUserLevel(UserDto $appUser, LanguageLevel $level): void
    {
        $this->updateAction->run(new UserDto(
            id: $appUser->id,
            name: $appUser->name,
            level: $level,
        ));
    }

    private function getText(LanguageLevel $level): string
    {
        $startCommand = BaseCommand::START;
        return <<<TEXT
Вы установили для себя уровень {$level->value}.
Для начала использования введите команду /{$startCommand->value}.
TEXT;
    }
}