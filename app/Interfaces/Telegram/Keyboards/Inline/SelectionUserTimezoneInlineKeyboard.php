<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Keyboards\Inline;

use App\Core\Modules\User\Vo\UtcOffset;
use App\Interfaces\Telegram\Commands\SettingsMenuCommand;
use App\Interfaces\Telegram\Parents\InlineKeyboard;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

final class SelectionUserTimezoneInlineKeyboard extends InlineKeyboard
{
    protected function rows(): array
    {
        $groups = ['negative' => [], 'zero' => null, 'positive' => []];
        $offsets = UtcOffset::all();

        foreach ($offsets as $offset) {
            $value = $offset->value();

            if ($value < 0) {
                $groups['negative'][] = $offset;
            } elseif ($value === 0) {
                $groups['zero'] = $offset;
            } else {
                $groups['positive'][] = $offset;
            }
        }

        return array_merge(
            $this->buildRows($groups['negative']),
            $groups['zero'] !== null ? [[$this->makeButton($groups['zero'])]] : [],
            $this->buildRows($groups['positive']),
        );
    }

    /**
     * @param UtcOffset[] $offsets
     * @return InlineKeyboardButton[][]
     */
    private function buildRows(array $offsets): array
    {
        return array_map(
            fn(array $chunk) => $this->makeButtons($chunk),
            array_chunk($offsets, 2)
        );
    }

    /**
     * @param UtcOffset[] $offsets
     * @return InlineKeyboardButton[]
     */
    private function makeButtons(array $offsets): array
    {
        return array_map(
            fn(UtcOffset $offset) => $this->makeButton($offset),
            $offsets
        );
    }

    private function makeButton(UtcOffset $offset): InlineKeyboardButton
    {
        return InlineKeyboardButton::make(
            text: $offset->format(),
            callback_data: SettingsMenuCommand::SET_TIMEZONE_CALLBACK->value . $offset->value(),
        );
    }
}