<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Message\Message;

final class StartHandler extends Handler
{
    protected function handle(Nutgram $bot, ...$parameters): ?Message
    {
        return $bot->sendMessage(
            text: $this->getText(),
            reply_markup: $this->getKeyboard(),
        );
    }

    private function getText(): string
    {
        return 'Добро пожаловать в Linguide - бот для изучения английского языка!';
    }

    private function getKeyboard(): ReplyKeyboardMarkup
    {
        return ReplyKeyboardMarkup::make()
            ->addRow(
                KeyboardButton::make('Кнопка 1'),
                KeyboardButton::make('Кнопка 2'),
            );
    }
}