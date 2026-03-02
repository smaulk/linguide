<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Parents;

use App\Interfaces\Telegram\Contracts\ReplyKeyboardContract;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

abstract class ReplyKeyboard implements ReplyKeyboardContract
{
    public function make(): ReplyKeyboardMarkup
    {
        $keyboard = ReplyKeyboardMarkup::make();
        foreach ($this->rows() as $row) {
            $keyboard->addRow(...$row);
        }

        return $keyboard;
    }

    /**
     * @return array<int, array<int, KeyboardButton>>
     */
    abstract protected function rows(): array;
}