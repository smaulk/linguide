<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\Dictionary\Enums\PartOfSpeech;
use App\Core\Modules\Dictionary\Enums\TermType;

final readonly class TermTranslationDatasetDto extends Dto
{
    /**
     * @param TranslationDatasetDto[] $translations
     */
    public function __construct(
        public string $text,
        public TermType $type,
        public PartOfSpeech $pos,
        public array $translations = [],
    ){}
}