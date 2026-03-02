<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Keyboards\Reply;

use App\Interfaces\Telegram\Commands\AiTalkCommand;
use App\Interfaces\Telegram\Parents\ReplyKeyboard;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

final class AiTalkReplyKeyboard extends ReplyKeyboard
{
    protected function rows(): array
    {
        return [
            [KeyboardButton::make(AiTalkCommand::STOP_TALK->value)],
        ];
    }
}