<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Core\Modules\User\Actions\UpdateUserSettingAction;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Dto\UserSettingDto;
use App\Core\Modules\User\Vo\WordsRepeatLimit;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;
use Throwable;

final class SetUserWordsRepeatLimitHandler extends Handler
{
    public function __construct(private readonly UpdateUserSettingAction $updateAction){}

    /**
     * @throws Throwable
     */
    public function __invoke(Nutgram $bot, int $limit): void
    {
        $bot->answerCallbackQuery();

        $wordsRepeatLimit = WordsRepeatLimit::fromInt($limit);
        $appUser = $this->getAppUser($bot);

        $this->updateUserWordsRepeatLimit($appUser, $wordsRepeatLimit);

        $bot->editMessageText($this->getText($wordsRepeatLimit));
    }

    /**
     * @throws Throwable
     */
    private function updateUserWordsRepeatLimit(UserDto $appUser, WordsRepeatLimit $limit): void
    {
        $this->updateAction->run($appUser->id, new UserSettingDto(
            level: $appUser->settings->level,
            utcOffset: $appUser->settings->utcOffset,
            wordsRepeatLimit: $limit,
        ));
    }

    private function getText(WordsRepeatLimit $limit): string
    {
        return "Вы установили для себя лимит: {$limit->value()}";
    }
}