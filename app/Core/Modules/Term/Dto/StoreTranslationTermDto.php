<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\Term\Enums\PartOfSpeech;
use App\Core\Modules\Term\Enums\TermType;

final readonly class StoreTranslationTermDto extends Dto
{
    /**
     * @param StoreTranslationDto[] $translations
     */
    public function __construct(
        public string $text,
        public TermType $type,
        public PartOfSpeech $pos,
        public array $translations = [],
    ){}
}