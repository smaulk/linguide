<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Contracts;

use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

interface ReplyKeyboardContract
{
    public function make(): ReplyKeyboardMarkup;
}