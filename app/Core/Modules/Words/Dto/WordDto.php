<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\Words\Enums\PartOfSpeechType;

final readonly class WordDto extends Dto
{
    /**
     * @param WordTranslationDto[]|null $translations
     */
    public function __construct(
        public string $text,
        public PartOfSpeechType $pos,
        public ?array $translations = null,
    ){}
}