<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Dto;

use App\Core\Common\Parents\Dto;

final readonly class InitializeDictionaryResultDto extends Dto
{
    public function __construct(
        public int $wordsCount,
        public ?ImportWordTranslationsResultDto $translationsResult,
    ){}
}