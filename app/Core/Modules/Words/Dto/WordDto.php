<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\User\Enums\LanguageLevel;
use App\Core\Modules\Words\Enums\PartOfSpeech;

final readonly class WordDto extends Dto
{
    /**
     * @param WordTranslationDto[] $translations
     */
    public function __construct(
        public int $id,
        public string $text,
        public PartOfSpeech $pos,
        public LanguageLevel $level,
        public array $translations = [],
    ){}
}