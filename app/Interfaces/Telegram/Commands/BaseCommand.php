<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Commands;

enum BaseCommand: string
{
    case START = 'start';
}