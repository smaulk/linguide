<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Core\Modules\User\Vo\WordsRepeatLimit;
use App\Interfaces\Telegram\Keyboards\Inline\ShowUserWordsRepeatLimitInlineKeyboard;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;

final class ShowUserWordsRepeatLimitHandler extends Handler
{
    public function __construct(private readonly ShowUserWordsRepeatLimitInlineKeyboard $keyboard){}

    public function __invoke(Nutgram $bot): void
    {
        $appUser = $this->getAppUser($bot);
        $settings = $appUser->settings;

        $bot->sendMessage(
            text: $this->getText($settings->wordsRepeatLimit),
            reply_markup: $this->keyboard->make(),
        );
    }

    private function getText(WordsRepeatLimit $limit): string
    {
        return "Ваше количество слов для повторения: {$limit->value()}.";
    }
}