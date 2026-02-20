<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\Ai\Enums\AiDriverType;

final readonly class AiAgentConfigDto extends Dto
{
    public function __construct(
        public string $instruction,
        public AiDriverType $driverType,
        public ?int $historyLimit = null,
        public ?string $name = null,
    ){}
}