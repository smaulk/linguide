<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Keyboards\Inline;

use App\Interfaces\Telegram\Commands\SettingsMenuCommand;
use App\Interfaces\Telegram\Parents\InlineKeyboard;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

final class ShowUserWordsRepeatLimitInlineKeyboard extends InlineKeyboard
{
    protected function rows(): array
    {
        return [
            [
                InlineKeyboardButton::make(
                    text: 'Изменить',
                    callback_data: SettingsMenuCommand::SELECT_WORDS_REPEAT_LIMIT_CALLBACK->value,
                ),
            ],
        ];
    }
}