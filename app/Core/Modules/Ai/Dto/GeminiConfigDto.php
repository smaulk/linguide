<?php
declare(strict_types=1);

namespace App\Core\Modules\Ai\Dto;

use App\Core\Common\Base\Dto;

final readonly class GeminiConfigDto extends Dto
{
    public function __construct(
        public string $apiKey,
        public string $apiVersion,
        public string $model,
    ){}
}