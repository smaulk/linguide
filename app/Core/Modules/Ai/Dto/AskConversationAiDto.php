<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\Ai\Enums\AiAgentType;
use App\Core\Modules\Ai\Enums\AiDriverType;

final readonly class AskConversationAiDto extends Dto
{
    /**
     * @param int $conversationId
     * @param string $content
     * @param array<string, mixed>|null $meta
     * @param int|null $tgMessageId
     */
    public function __construct(
        public int $conversationId,
        public string $content,
        public ?array $meta = null,
        public ?int $tgMessageId = null,
    ){}
}