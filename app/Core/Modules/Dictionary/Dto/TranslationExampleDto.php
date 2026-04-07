<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Dto;

use App\Core\Common\Parents\Dto;

final readonly class TranslationExampleDto extends Dto
{
    public function __construct(
        public int $id,
        public string $sentence_en,
        public string $sentence_ru,
    ){}
}