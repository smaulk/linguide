<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Commands;

enum MainMenuCommand: string
{
    case START_TALK   = '💬 Поговорить';
    case REVIEW_WORDS = '🔁 Повторить слова';
    case STATISTICS   = '📊 Статистика';
    case SETTINGS = '⚙️ Настройки';
}