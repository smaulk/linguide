<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Dto;

use App\Core\Common\Parents\Dto;

final readonly class WordTranslationDto extends Dto
{
    /**
     * @param TranslationExampleDto[]|null $examples
     */
    public function __construct(
        public string $text,
        public string $context_en,
        public string $context_ru,
        public ?array $examples = null,
    ){}
}