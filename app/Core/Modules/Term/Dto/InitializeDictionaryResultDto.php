<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Dto;

use App\Core\Common\Parents\Dto;

final readonly class InitializeDictionaryResultDto extends Dto
{
    public function __construct(
        public ImportTermsResultDto $termsResult,
        public ?ImportTranslationsResultDto $translationsResult,
    ){}
}