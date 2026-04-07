<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Core\Modules\User\Actions\UpdateUserSettingAction;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Dto\UserSettingsDto;
use App\Core\Modules\User\Vo\WordsReviewLimit;
use App\Interfaces\Telegram\Classes\AppUserContext;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;
use Throwable;

final class SetUserWordsReviewLimitHandler extends Handler
{
    public function __construct(
        private readonly AppUserContext $userContext,
        private readonly UpdateUserSettingAction $updateAction
    ){}

    /**
     * @throws Throwable
     */
    public function __invoke(Nutgram $bot, int $limit): void
    {
        $bot->answerCallbackQuery();

        $wordsReviewLimit = WordsReviewLimit::fromInt($limit);
        $appUser = $this->userContext->get($bot);

        $this->updateUserWordsReviewLimit($appUser, $wordsReviewLimit);

        $bot->editMessageText($this->getText($wordsReviewLimit));
    }

    /**
     * @throws Throwable
     */
    private function updateUserWordsReviewLimit(UserDto $appUser, WordsReviewLimit $limit): void
    {
        $this->updateAction->run($appUser->id, new UserSettingsDto(
            level: $appUser->settings->level,
            utcOffset: $appUser->settings->utcOffset,
            wordsReviewLimit: $limit,
        ));
    }

    private function getText(WordsReviewLimit $limit): string
    {
        return "Вы установили для себя лимит: {$limit->value()}";
    }
}