<?php
declare(strict_types=1);

namespace App\Core\Modules\AiConversation\Dto;

use App\Core\Common\Parents\Dto;

final readonly class CreateAiMessagesDto extends Dto
{
    /**
     * @param AiMessageDto[] $messages
     */
    public function __construct(
        public int $conversationId,
        public array $messages,
    ){}
}