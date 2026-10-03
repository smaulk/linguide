<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Keyboards\Inline;

use App\Interfaces\Telegram\Commands\SettingsMenuCommand;
use App\Interfaces\Telegram\Parents\InlineKeyboard;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

final class SelectionUserRemindersInlineKeyboard extends InlineKeyboard
{
    protected function rows(): array
    {
        return [
            [$this->makeButton('Включить', 1)],
            [$this->makeButton('Выключить', 0)],
        ];
    }

    private function makeButton(string $title, int $value): InlineKeyboardButton
    {
        return InlineKeyboardButton::make(
            text: $title,
            callback_data: SettingsMenuCommand::SET_REMINDERS_CALLBACK->value . $value,
        );
    }
}