<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\Term\Enums\PartOfSpeech;
use App\Core\Modules\Term\Enums\TermType;

final readonly class ValidatedCandidateDto extends Dto
{
    /**
     * @param PartOfSpeech[]|null $pos
     */
    public function __construct(
        public string $term,
        public bool $isValid,
        public ?TermType $type,
        public ?string $baseForm,
        public ?array $pos,
    ){}
}