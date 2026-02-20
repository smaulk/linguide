<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\AiConversation\Dto\AiMessageDto;

final readonly class AiRequestDto extends Dto
{
    /**
     * @param AiMessageDto[] $messages
     */
    public function __construct(
        public array $messages,
        public ?string $instruction = null,
    ) {}
}