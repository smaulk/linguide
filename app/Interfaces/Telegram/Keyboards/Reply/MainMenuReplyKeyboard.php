<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Keyboards\Reply;

use App\Interfaces\Telegram\Commands\MainMenuCommand;
use App\Interfaces\Telegram\Parents\ReplyKeyboard;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

final class MainMenuReplyKeyboard extends ReplyKeyboard
{
    public function make(): ReplyKeyboardMarkup
    {
        return ReplyKeyboardMarkup::make()
            ->addRow(
                KeyboardButton::make(MainMenuCommand::START_TALK->value),
            );
    }
}