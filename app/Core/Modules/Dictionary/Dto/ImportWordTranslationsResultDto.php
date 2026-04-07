<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Dto;

use App\Core\Common\Parents\Dto;

final readonly class ImportWordTranslationsResultDto extends Dto
{
    public function __construct(
        public int $words,
        public int $translations,
        public int $examples,
    ){}
}