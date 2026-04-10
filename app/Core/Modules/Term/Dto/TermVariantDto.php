<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\Term\Enums\TermType;
use App\Core\Modules\User\Enums\LanguageLevel;
use App\Core\Modules\Term\Enums\PartOfSpeech;

final readonly class TermVariantDto extends Dto
{
    /**
     * @param TranslationDto[] $translations
     */
    public function __construct(
        public int $id,
        public string $text,
        public TermType $type,
        public PartOfSpeech $pos,
        public ?LanguageLevel $level,
        public array $translations = [],
    ){}
}