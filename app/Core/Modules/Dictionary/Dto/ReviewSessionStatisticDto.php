<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\Dictionary\Vo\Duration;

final readonly class ReviewSessionStatisticDto extends Dto
{
    public function __construct(
        public Duration $duration,
        public int $termsCount,
        public int $correctTermsCount,
        public Duration $avgResponseTime,
        public Duration $maxResponseTime,
        public Duration $minResponseTime,
    ){}
}