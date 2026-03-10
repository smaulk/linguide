<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\Words\Enums\PartOfSpeech;

final readonly class WordDatasetDto extends Dto
{
    /**
     * @param WordTranslationDatasetDto[] $translations
     */
    public function __construct(
        public string $text,
        public PartOfSpeech $pos,
        public array $translations = [],
    ){}
}