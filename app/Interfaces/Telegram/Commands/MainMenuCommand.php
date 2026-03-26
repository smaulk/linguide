<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Commands;

enum MainMenuCommand: string
{
    case START_TALK = '💬 Поговорить';
    case REPEAT_WORDS = '🔁 Повторить слова';
    case SETTINGS = '⚙️ Настройки';
}