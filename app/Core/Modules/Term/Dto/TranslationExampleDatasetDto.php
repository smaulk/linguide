<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Dto;

use App\Core\Common\Parents\Dto;

final readonly class TranslationExampleDatasetDto extends Dto
{
    public function __construct(
        public string $sentence_en,
        public string $sentence_ru,
    ){}
}