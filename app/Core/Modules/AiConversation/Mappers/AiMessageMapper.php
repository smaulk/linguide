<?php
declare(strict_types=1);

namespace App\Core\Modules\AiConversation\Mappers;

use App\Core\Modules\AiConversation\Dto\AiMessageDto;
use App\Core\Modules\AiConversation\Models\AiMessage;

final class AiMessageMapper
{
    public function mapModelToDto(AiMessage $message): AiMessageDto
    {
        return new AiMessageDto(
            role: $message->role,
            content: $message->content,
            tgMessageId: $message->telegram_message_id,
            meta: $message->meta,
        );
    }

    /**
     * @param iterable<AiMessage> $messages
     * @return AiMessageDto[]
     */
    public function mapModelsToDtoArray(iterable $messages): array
    {
        $result = [];
        foreach ($messages as $message) {
            $result[] = $this->mapModelToDto($message);
        }

        return $result;
    }
}