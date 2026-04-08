<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Core\Modules\User\Actions\UpdateUserSettingAction;
use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Dto\UserSettingsDto;
use App\Core\Modules\User\Vo\ReviewLimit;
use App\Interfaces\Telegram\Classes\AppUserContext;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;
use Throwable;

final class SetUserReviewLimitHandler extends Handler
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

        $reviewLimit = ReviewLimit::fromInt($limit);
        $appUser = $this->userContext->get($bot);

        $this->updateUserReviewLimit($appUser, $reviewLimit);

        $bot->editMessageText($this->getText($reviewLimit));
    }

    /**
     * @throws Throwable
     */
    private function updateUserReviewLimit(UserDto $appUser, ReviewLimit $limit): void
    {
        $this->updateAction->run($appUser->id, new UserSettingsDto(
            level: $appUser->settings->level,
            utcOffset: $appUser->settings->utcOffset,
            reviewLimit: $limit,
        ));
    }

    private function getText(ReviewLimit $limit): string
    {
        return "Вы установили для себя лимит: {$limit->value()}";
    }
}