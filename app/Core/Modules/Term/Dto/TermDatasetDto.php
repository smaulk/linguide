<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\Term\Enums\TermType;
use App\Core\Modules\User\Enums\LanguageLevel;
use App\Core\Modules\Term\Enums\PartOfSpeech;

final readonly class TermDatasetDto extends Dto
{
    public function __construct(
        public string $text,
        public TermType $type,
        public LanguageLevel $level,
        public PartOfSpeech $pos,
    ){}
}