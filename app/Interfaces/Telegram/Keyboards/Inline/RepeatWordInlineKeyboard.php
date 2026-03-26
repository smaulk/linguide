<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Keyboards\Inline;

use App\Interfaces\Telegram\Parents\InlineKeyboard;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

final class RepeatWordInlineKeyboard extends InlineKeyboard
{
    protected function rows(): array
    {
        return [
            [
                InlineKeyboardButton::make(
                    text: 'Не помню',
                    callback_data: 'word:forgot',
                ),
            ],
        ];
    }
}