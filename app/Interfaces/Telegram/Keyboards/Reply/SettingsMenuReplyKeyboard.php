<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Keyboards\Reply;

use App\Interfaces\Telegram\Commands\BaseCommand;
use App\Interfaces\Telegram\Commands\SettingsMenuCommand;
use App\Interfaces\Telegram\Parents\ReplyKeyboard;
use SergiX44\Nutgram\Telegram\Properties\ButtonStyle;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;

final class SettingsMenuReplyKeyboard extends ReplyKeyboard
{
    protected function rows(): array
    {
        return [
            [
                KeyboardButton::make(
                    text: SettingsMenuCommand::TIMEZONE->value,
                    style: ButtonStyle::SUCCESS,
                ),
            ],
            [
                KeyboardButton::make(
                    text: SettingsMenuCommand::LEVEL->value,
                    style: ButtonStyle::SUCCESS,
                ),
            ],
            [
                KeyboardButton::make(
                    text: SettingsMenuCommand::WORDS_REPEAT_LIMIT->value,
                    style: ButtonStyle::SUCCESS,
                ),
            ],
            [
                KeyboardButton::make(
                    text: BaseCommand::MAIN_MENU->value,
                    style: ButtonStyle::PRIMARY,
                ),
            ]
        ];
    }
}