<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Dto;

use App\Core\Common\Base\Dto;

final readonly class AiRequestDto extends Dto
{
    public function __construct(
        public string $message,
        public ?string $instruction = null,
    ){}
}