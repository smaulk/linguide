<?php
declare(strict_types=1);

namespace App\Core\Modules\AiConversation\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\AiConversation\Enums\AiMessageRole;

final readonly class AiMessageDto extends Dto
{
    /**
     * @param AiMessageRole $role
     * @param string $content
     * @param int|null $tgMessageId
     * @param array<string, mixed>|null $meta
     */
    public function __construct(
        public string $content,
        public AiMessageRole $role = AiMessageRole::USER,
        public ?int $tgMessageId = null,
        public ?array $meta = null,
    ){}
}