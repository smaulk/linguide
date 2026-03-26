<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Keyboards\Inline;

use App\Core\Modules\User\Vo\WordsRepeatLimit;
use App\Interfaces\Telegram\Commands\SettingsMenuCommand;
use App\Interfaces\Telegram\Parents\InlineKeyboard;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

final class SelectionUserWordsRepeatLimitInlineKeyboard extends InlineKeyboard
{
    private const array LIMIT_LIST = [2, 3, 5, 7, 10, 12, 15, 20, 25, 30, 40, 50];

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
            fn(int $limit) => $this->makeButton(WordsRepeatLimit::fromInt($limit)),
            self::LIMIT_LIST,
        );
    }

    private function makeButton(WordsRepeatLimit $limit): InlineKeyboardButton
    {
        return InlineKeyboardButton::make(
            text: (string)$limit->value(),
            callback_data: SettingsMenuCommand::SET_WORDS_REPEAT_LIMIT_CALLBACK->value . $limit->value(),
        );
    }
}