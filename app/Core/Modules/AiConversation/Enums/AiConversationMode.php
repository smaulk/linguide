<?php
declare(strict_types=1);

namespace App\Core\Modules\AiConversation\Enums;

use App\Core\Common\Concerns\BaseEnum;
use App\Core\Modules\Ai\Enums\AiAgentType;

enum AiConversationMode: string
{
    use BaseEnum;

    case TALKING = 'talking';

    public function agentType(): AiAgentType
    {
        return match ($this) {
            self::TALKING => AiAgentType::ENGLISH_TALK,
        };
    }
}
