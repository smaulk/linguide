<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Keyboards\Inline;

use App\Core\Modules\User\Enums\LanguageLevel;
use App\Interfaces\Telegram\Commands\SettingsMenuCommand;
use App\Interfaces\Telegram\Parents\InlineKeyboard;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

final class SelectionUserLevelInlineKeyboard extends InlineKeyboard
{
    protected function rows(): array
    {
        return array_chunk($this->makeButtons(), 2);
    }

    /**
     * @return InlineKeyboardButton[]
     */
    private function makeButtons(): array
    {
        return array_map(
            fn(LanguageLevel $level) => $this->makeButton($level),
            LanguageLevel::cases(),
        );
    }

    private function makeButton(LanguageLevel $level): InlineKeyboardButton
    {
        return InlineKeyboardButton::make(
            text: $level->name,
            callback_data: SettingsMenuCommand::SET_LEVEL_CALLBACK->value . $level->value,
        );
    }
}