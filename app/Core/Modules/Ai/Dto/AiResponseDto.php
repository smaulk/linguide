<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Dto;

use App\Core\Common\Parents\Dto;

final readonly class AiResponseDto extends Dto
{
    public function __construct(
        public string $text,
        public string $role,
        public string $finishReason,
    ){}
}