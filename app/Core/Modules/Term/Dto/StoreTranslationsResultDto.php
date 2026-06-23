<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Dto;

use App\Core\Common\Parents\Dto;

final readonly class StoreTranslationsResultDto extends Dto
{
    public function __construct(
        public int $variantsCount,
        public int $translationsCount,
        public int $examplesCount,
    ){}
}