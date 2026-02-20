<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\Ai\Enums\AiAgentType;

final readonly class AskAiDto extends Dto
{
    /**
     * @param AiAgentType $agentType
     * @param string $content
     * @param array<string, mixed>|null $meta
     */
    public function __construct(
        public AiAgentType $agentType,
        public string $content,
        public ?array $meta = null,
    ){}
}