<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Dto;

use App\Core\Common\Base\Dto;
use App\Core\Modules\Ai\Enums\AiDriverType;

final readonly class AskAiDto extends Dto
{
    public function __construct(
        public string $message,
        public AiDriverType $providerType,
        public array $providerConfig = [],
    ){}
}