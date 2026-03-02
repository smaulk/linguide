<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Keyboards\Reply;

use App\Interfaces\Telegram\Commands\MainMenuCommand;
use App\Interfaces\Telegram\Parents\ReplyKeyboard;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use \SergiX44\Nutgram\Telegram\Properties\ButtonStyle;

final class MainMenuReplyKeyboard extends ReplyKeyboard
{
    protected function rows(): array
    {
        return [
            [KeyboardButton::make(
                text: MainMenuCommand::START_TALK->value,
                style: ButtonStyle::PRIMARY,
            )]
        ];
    }
}