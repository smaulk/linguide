<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Dto;

use App\Core\Common\Parents\Dto;

final readonly class AiDriverConfigDto extends Dto
{
    public function __construct(
        public string $apiKey,
        public string $model,
        public ?string $apiVersion = null,
    ){}
}