<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Contracts;

use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

interface InlineKeyboardContract
{
    public function make(): InlineKeyboardMarkup;
}