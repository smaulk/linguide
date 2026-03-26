<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Commands;

enum SettingsMenuCommand: string
{
    case LEVEL              = '🎯 Уровень знания';
    case TIMEZONE           = '🕒 Часовой пояс';
    case WORDS_REPEAT_LIMIT = "🔢 Количество слов для повторения";

    case SELECT_LEVEL_CALLBACK              = 'select-level:';
    case SELECT_TIMEZONE_CALLBACK           = 'select-timezone-offset:';
    case SELECT_WORDS_REPEAT_LIMIT_CALLBACK = 'select-words-repeat-limit:';

    case SET_LEVEL_CALLBACK              = 'set-level:';
    case SET_TIMEZONE_CALLBACK           = 'set-timezone-offset:';
    case SET_WORDS_REPEAT_LIMIT_CALLBACK = 'set-words-repeat-limit:';
}