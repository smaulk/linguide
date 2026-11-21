<?php

declare(strict_types=1);

namespace App\Core\Modules\Term\Dto;

use App\Core\Common\Parents\Dto;

final readonly class ReviewPeriodStatisticsDto extends Dto
{
    public function __construct(
        public int $sessionsCount,
        public int $answeredTermsCount,
        public float $correctAnswersPercent,
        public int $wellLearnedWordsCount,
        public int $activeDaysCount,
        public int $longestActivityStreak,
    ) {}
}
