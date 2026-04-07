<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\User\Enums\LanguageLevel;
use App\Core\Modules\Dictionary\Enums\PartOfSpeech;

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