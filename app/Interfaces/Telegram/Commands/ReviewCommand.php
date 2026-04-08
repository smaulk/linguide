<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Commands;

enum ReviewCommand: string
{
    case FORGOT = 'review:forgot';
}