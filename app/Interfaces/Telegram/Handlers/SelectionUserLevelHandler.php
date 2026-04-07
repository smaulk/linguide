<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use App\Interfaces\Telegram\Classes\CallbackParser;
use App\Interfaces\Telegram\Commands\SettingsMenuCommand;
use App\Interfaces\Telegram\Keyboards\Inline\SelectionUserLevelInlineKeyboard;
use App\Interfaces\Telegram\Parents\Handler;
use SergiX44\Nutgram\Nutgram;

final class SelectionUserLevelHandler extends Handler
{
    public function __construct(private readonly SelectionUserLevelInlineKeyboard $keyboard){}

    public function __invoke(Nutgram $bot): void
    {
        $text = $this->getText();
        $keyboard = $this->keyboard->make();
        $callback = $bot->callbackQuery()?->data;

        if ($callback !== null
            && CallbackParser::isMatch($callback, SettingsMenuCommand::SELECT_LEVEL_CALLBACK->value)
        ) {
            $bot->answerCallbackQuery();
            $bot->editMessageText(text: $text, reply_markup: $keyboard);

            return;
        }

        $bot->sendMessage(text: $text, reply_markup: $keyboard);
    }

    private function getText(): string
    {
        return 'Выберите ваш уровень знания английского языка:';
    }
}