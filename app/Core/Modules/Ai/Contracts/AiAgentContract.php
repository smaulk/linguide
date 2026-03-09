<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Contracts;

use App\Core\Modules\Ai\Dto\AiResponseDto;
use App\Core\Modules\AiConversation\Dto\AiMessageDto;

interface AiAgentContract
{
    /**
     * @param AiMessageDto|AiMessageDto[] $messages
     * @return AiResponseDto
     */
    public function send(AiMessageDto|array $messages): AiResponseDto;

    public function getHistoryLimit(): int;

    public function getName(): ?string;
}