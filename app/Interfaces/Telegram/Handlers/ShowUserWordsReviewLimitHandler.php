<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Core\Modules\User\Vo\WordsReviewLimit;
use App\Interfaces\Telegram\Classes\AppUserContext;
use App\Interfaces\Telegram\Keyboards\Inline\ShowUserWordsReviewLimitInlineKeyboard;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;

final class ShowUserWordsReviewLimitHandler extends Handler
{
    public function __construct(
        private readonly AppUserContext $userContext,
        private readonly ShowUserWordsReviewLimitInlineKeyboard $keyboard
    ){}

    public function __invoke(Nutgram $bot): void
    {
        $appUser = $this->userContext->get($bot);
        $settings = $appUser->settings;

        $bot->sendMessage(
            text: $this->getText($settings->wordsReviewLimit),
            reply_markup: $this->keyboard->make(),
        );
    }

    private function getText(WordsReviewLimit $limit): string
    {
        return "Ваше количество слов для повторения: {$limit->value()}.";
    }
}