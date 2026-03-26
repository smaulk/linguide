<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Interfaces\Telegram\Keyboards\Inline\SelectionUserTimezoneInlineKeyboard;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;

final class SelectionUserTimezoneHandler extends Handler
{
    public function __construct(private readonly SelectionUserTimezoneInlineKeyboard $keyboard){}

    public function __invoke(Nutgram $bot): void
    {
        $text = $this->getText();
        $keyboard = $this->keyboard->make();

        if (!$bot->isCallbackQuery()) {
            $bot->sendMessage(text: $text, reply_markup: $keyboard);
            return;
        }

        $bot->answerCallbackQuery();
        $bot->editMessageText(text: $text);
        $bot->editMessageReplyMarkup(reply_markup: $keyboard);
    }

    private function getText(): string
    {
        return 'Выберите ваш часовой пояс:';
    }
}