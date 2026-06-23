<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Dto;

use App\Core\Common\Parents\Dto;

final readonly class StoreTranslationDto extends Dto
{
    /**
     * @param StoreTranslationExampleDto[] $examples
     */
    public function __construct(
        public string $text,
        public string $contextEn,
        public string $contextRu,
        public array $examples = [],
    ){}
}