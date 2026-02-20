<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Commands;

enum AiTalkCommand: string
{
    case STOP_TALK = 'Завершить разговор';
}