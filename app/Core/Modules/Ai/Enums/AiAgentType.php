<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Enums;

enum AiAgentType: string
{
    case ENGLISH_TALK    = 'english_talk'; // Агент для общих разговоров по английскому
    case WORD_TRANSLATOR = 'word_translator'; // Агент для перевода слов
}
