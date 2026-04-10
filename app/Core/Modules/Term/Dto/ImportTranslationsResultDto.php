<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Dto;

use App\Core\Common\Parents\Dto;

final readonly class ImportTranslationsResultDto extends Dto
{
    public function __construct(
        public int $variants,
        public int $translations,
        public int $examples,
    ){}
}