<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Core\Modules\User\Vo\ReviewLimit;
use App\Interfaces\Telegram\Classes\AppUserContext;
use App\Interfaces\Telegram\Keyboards\Inline\ShowUserReviewLimitInlineKeyboard;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;

final class ShowUserReviewLimitHandler extends Handler
{
    public function __construct(
        private readonly AppUserContext $userContext,
        private readonly ShowUserReviewLimitInlineKeyboard $keyboard
    ){}

    public function __invoke(Nutgram $bot): void
    {
        $appUser = $this->userContext->get($bot);
        $settings = $appUser->settings;

        $bot->sendMessage(
            text: $this->getText($settings->reviewLimit),
            reply_markup: $this->keyboard->make(),
        );
    }

    private function getText(ReviewLimit $limit): string
    {
        return "Ваше количество терминов для повторения: {$limit->value()}.";
    }
}