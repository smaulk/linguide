<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Keyboards\Reply;

use App\Interfaces\Telegram\Commands\MainMenuCommand;
use App\Interfaces\Telegram\Parents\ReplyKeyboard;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use \SergiX44\Nutgram\Telegram\Properties\ButtonStyle;
use function Laravel\Prompts\text;

final class MainMenuReplyKeyboard extends ReplyKeyboard
{
    protected function rows(): array
    {
        return [
            [
                KeyboardButton::make(
                    text: MainMenuCommand::REVIEW_WORDS->value,
                    style: ButtonStyle::SUCCESS,
                ),
                KeyboardButton::make(
                    text: MainMenuCommand::START_TALK->value,
                    style: ButtonStyle::SUCCESS,
                ),
            ],
            [
                // KeyboardButton::make(
                //     text: MainMenuCommand::STATISTICS->value,
                //     style: ButtonStyle::PRIMARY,
                // ),
                KeyboardButton::make(
                    text: MainMenuCommand::SETTINGS->value,
                    style: ButtonStyle::PRIMARY,
                ),
            ],
        ];
    }
}