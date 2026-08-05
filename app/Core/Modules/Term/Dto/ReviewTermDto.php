<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\Term\Enums\ReviewMode;

final readonly class ReviewTermDto extends Dto
{
    public function __construct(
        public LearningProgressDto $learningProgress,
        public ReviewMode $mode,
    ){}
}