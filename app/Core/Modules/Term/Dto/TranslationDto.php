<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Dto;

use App\Core\Common\Parents\Dto;

final readonly class TranslationDto extends Dto
{
    /**
     * @param TranslationExampleDto[] $examples
     */
    public function __construct(
        public int $id,
        public string $text,
        public string $context_en,
        public string $context_ru,
        public array $examples = [],
    ){}
}