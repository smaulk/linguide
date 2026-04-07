<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Commands;

enum ReviewWordsCommand: string
{
    case FORGOT_WORD = 'word:forgot';
}