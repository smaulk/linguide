<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Dto;

use App\Core\Common\Parents\Dto;

final readonly class DictionaryImportConfigDto extends Dto
{
    public function __construct(
        public string $termsResource,
        public ?string $translationsResource,
    ){}
}