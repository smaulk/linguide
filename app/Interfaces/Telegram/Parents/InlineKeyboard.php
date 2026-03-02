<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Parents;

use App\Interfaces\Telegram\Contracts\InlineKeyboardContract;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

abstract class InlineKeyboard implements InlineKeyboardContract
{
    public function make(): InlineKeyboardMarkup
    {
        $keyboard = InlineKeyboardMarkup::make();
        foreach ($this->rows() as $row) {
            $keyboard->addRow(...$row);
        }

        return $keyboard;
    }

    /**
     * @return array<int, array<int, InlineKeyboardButton>>
     */
    abstract protected function rows(): array;
}