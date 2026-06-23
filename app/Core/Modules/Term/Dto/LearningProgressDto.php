<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Dto;

use App\Core\Common\Parents\Dto;
use Illuminate\Support\Carbon;

final readonly class LearningProgressDto extends Dto
{
    public function __construct(
        public int $id,
        public int $repetitions,
        public int $interval,
        public float $easeFactor,
        public Carbon $dueAt,
        public ?Carbon $lastReviewedAt,
        public ?Carbon $createdAt,
        public TermVariantDto $termVariant,
    ){}
}