<?php
declare(strict_types=1);

namespace App\Core\Modules\AiConversation\Enums;

use App\Core\Common\Concerns\BaseEnum;

enum AiMessageRole: string
{
    use BaseEnum;

    case USER = 'user';
    case ASSISTANT = 'assistant';
    case SYSTEM = 'system';
}
