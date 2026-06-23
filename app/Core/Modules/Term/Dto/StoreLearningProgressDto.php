<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Dto;

use App\Core\Common\Parents\Dto;

final readonly class StoreLearningProgressDto extends Dto
{
    /**
     * @param int[] $termVariantIds
     */
    public function __construct(
        public int $userId,
        public array $termVariantIds,
    ){}
}