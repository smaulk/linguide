<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Dto;

use App\Core\Common\Parents\Dto;

final readonly class StoreTermsResultDto extends Dto
{
    /**
     * @param array<string, int> $termIdsByText
     */
    public function __construct(
        public array $termIdsByText,
        public int $termsCount,
        public int $variantsCount,
    ){}
}