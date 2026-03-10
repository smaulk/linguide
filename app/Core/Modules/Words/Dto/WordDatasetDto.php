<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\Words\Enums\PartOfSpeechType;

final readonly class WordDatasetDto extends Dto
{
    /**
     * @param WordTranslationDatasetDto[]|null $translations
     */
    public function __construct(
        public string $text,
        public PartOfSpeechType $pos,
        public ?array $translations = null,
    ){}
}